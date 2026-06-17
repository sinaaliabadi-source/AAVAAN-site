<?php

namespace App\Http\Controllers;

use App\Models\ArtistSubscription;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Exception;

class ArtistSubscriptionController extends Controller
{
    public function __construct(private PaymentService $payment) {}

    public function index()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription();
        $history = $user->subscriptions()->latest()->limit(10)->get();
        $prices = config('aavaan.artist_subscription');
        return view('dashboard.artist.subscription', compact('subscription', 'history', 'prices'));
    }

    public function pay(Request $request)
    {
        $validated = $request->validate(['plan' => 'required|in:monthly,yearly']);
        $prices = config('aavaan.artist_subscription');
        $amount = $validated['plan'] === 'yearly' ? $prices['yearly_price'] : $prices['monthly_price'];
        $description = $validated['plan'] === 'yearly' ? 'اشتراک سالانه آوان' : 'اشتراک ماهانه آوان';

        $sub = ArtistSubscription::create([
            'user_id'        => auth()->id(),
            'plan'           => $validated['plan'],
            'amount'         => $amount,
            'payment_status' => 'pending',
        ]);

        try {
            $result = $this->payment->request(
                $amount,
                $description,
                route('artist.subscription.callback') . '?sub=' . $sub->id
            );
            $sub->update(['payment_authority' => $result['authority']]);
            return redirect($result['redirect_url']);
        } catch (Exception $e) {
            $sub->update(['payment_status' => 'failed']);
            return back()->with('error', 'خطا در اتصال به درگاه پرداخت: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $sub = ArtistSubscription::findOrFail($request->query('sub'));
        if ($request->query('Status') !== 'OK') {
            $sub->update(['payment_status' => 'failed']);
            return redirect()->route('artist.subscription')->with('error', 'پرداخت لغو شد.');
        }
        try {
            $refId = $this->payment->verify($sub->payment_authority, $sub->amount);
            $duration = $sub->plan === 'yearly' ? 365 : 30;
            $sub->update([
                'payment_status' => 'paid',
                'payment_ref'    => $refId,
                'starts_at'      => now(),
                'expires_at'     => now()->addDays($duration),
            ]);
            auth()->user()->artistProfile?->update(['is_active' => true]);
            return redirect()->route('artist.subscription')->with('success', "پرداخت موفق. کد پیگیری: {$refId}");
        } catch (Exception $e) {
            $sub->update(['payment_status' => 'failed']);
            return redirect()->route('artist.subscription')->with('error', 'خطا در تأیید پرداخت: ' . $e->getMessage());
        }
    }
}
