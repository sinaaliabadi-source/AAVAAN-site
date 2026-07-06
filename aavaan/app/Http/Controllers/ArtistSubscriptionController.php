<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;
use App\Models\DiscountCode;
use App\Models\DiscountCodeUse;
use App\Models\Payment;
use App\Models\Subscription;
use App\Support\Festival;
use Illuminate\Http\Request;

class ArtistSubscriptionController extends Controller
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    public function index()
    {
        $user         = auth()->user();
        $subscription = $user->activeSubscription();
        $history      = $user->subscriptions()->with('payment')->latest()->limit(10)->get();
        $prices       = config('aavaan.artist_subscription');
        $festivalActive = Festival::active();
        $festivalEndsFa = Festival::endsAtJalali();

        return view('dashboard.artist.subscription', compact(
            'subscription', 'history', 'prices', 'festivalActive', 'festivalEndsFa'
        ));
    }

    /**
     * اعتبارسنجی کد تخفیف/جشنواره (JSON) — کاملاً سمت سرور. کد مصرف/سوزانده نمی‌شود؛
     * فقط بررسی اعتبار و محاسبهٔ قیمت جدید هر پلن برای نمایش.
     */
    public function validateDiscount(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $code = DiscountCode::whereRaw('LOWER(code) = LOWER(?)', [trim($request->input('code'))])->first();

        if (! $code) {
            return response()->json(['valid' => false, 'message' => 'کد تخفیف یافت نشد.']);
        }

        if ($error = $code->validationError()) {
            return response()->json(['valid' => false, 'message' => $error]);
        }

        $prices = config('aavaan.artist_subscription');
        $plans  = [
            'monthly' => [
                'original' => (int) $prices['monthly_price'],
                'final'    => $code->discountedAmount((int) $prices['monthly_price']),
            ],
            'yearly' => [
                'original' => (int) $prices['yearly_price'],
                'final'    => $code->discountedAmount((int) $prices['yearly_price']),
            ],
        ];

        $festival = Festival::active();

        return response()->json([
            'valid'           => true,
            'code'            => $code->code,
            'festival_active' => $festival,
            'plans'           => $plans,
            'message'         => $festival
                ? 'در حال حاضر به مناسبت جشنواره، عضویت رایگان است؛ کد شما برای پس از جشنواره قابل استفاده خواهد بود.'
                : 'کد تخفیف با موفقیت اعمال شد.',
        ]);
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'plan'          => 'required|in:monthly,yearly',
            'discount_code' => 'nullable|string|max:50',
        ]);
        $prices = config('aavaan.artist_subscription');
        $plan   = $validated['plan'];
        $amount = $plan === 'yearly' ? (int) $prices['yearly_price'] : (int) $prices['monthly_price'];
        $label  = $plan === 'yearly' ? 'سالانه' : 'ماهانه';

        // ── اعمال کد تخفیف (اعتبارسنجی مجدد سمت سرور) ──
        // در حالت جشنواره کد اعمال/سوزانده نمی‌شود (عضویت رایگان است؛ کد برای پس از جشنواره می‌ماند).
        $discountCode = null;
        if (! Festival::active() && ! empty($validated['discount_code'])) {
            $candidate = DiscountCode::whereRaw('LOWER(code) = LOWER(?)', [trim($validated['discount_code'])])->first();
            if ($candidate && $candidate->isValid()) {
                $discountCode = $candidate;
                $amount = $candidate->discountedAmount($amount);
            }
        }

        $sub = Subscription::create([
            'user_id' => auth()->id(),
            'plan'    => $plan,
            'status'  => 'pending',
        ]);

        $payment = $sub->payment()->create([
            'user_id' => auth()->id(),
            'amount'  => $amount,
            'gateway' => config('payment.driver', 'zarinpal'),
            'status'  => 'pending',
        ]);

        // کدِ در انتظارِ مصرف تا مرحلهٔ موفقیتِ پرداخت در session نگهداری می‌شود (مصرف اتمیک در callback).
        if ($discountCode) {
            session(['pending_discount_' . $payment->id => $discountCode->id]);
        }

        try {
            $result = $this->gateway->initiate(
                $amount,
                "اشتراک {$label} آوان",
                route('artist.subscription.callback', ['payment' => $payment->id])
            );
            $payment->update(['authority' => $result['authority']]);
            return redirect($result['redirect_url']);
        } catch (PaymentException $e) {
            $payment->update(['status' => 'failed']);
            return back()->with('error', 'خطا در اتصال به درگاه پرداخت: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $payment = Payment::with('payable')->findOrFail($request->query('payment'));
        $sub     = $payment->payable;

        if ($request->query('Status') !== 'OK') {
            $payment->update(['status' => 'failed']);
            return redirect()->route('payment.failed')->with('error', 'پرداخت لغو یا ناموفق بود.');
        }

        try {
            $refId = $this->gateway->verify($payment->authority, $payment->amount);
            $payment->markPaid($refId);
            $sub->activate();
            auth()->user()->artistProfile?->update(['is_active' => true]);

            // مصرفِ اتمیکِ کد تخفیف پس از موفقیتِ پرداخت (جلوگیری از race روی سقف استفاده).
            $sessionKey = 'pending_discount_' . $payment->id;
            $discountId = session($sessionKey);
            if ($discountId) {
                session()->forget($sessionKey);
                if (DiscountCode::consumeAtomically((int) $discountId)) {
                    DiscountCodeUse::create([
                        'discount_code_id' => (int) $discountId,
                        'user_id'          => $payment->user_id,
                        'subscription_id'  => $sub->id,
                        'used_at'          => now(),
                    ]);
                }
            }

            return redirect()->route('payment.success')->with([
                'ref_id'  => $refId,
                'context' => 'اشتراک ' . ($sub->plan === 'yearly' ? 'سالانه' : 'ماهانه') . ' آوان',
            ]);
        } catch (PaymentException $e) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('payment.failed')->with('error', $e->getMessage());
        }
    }
}
