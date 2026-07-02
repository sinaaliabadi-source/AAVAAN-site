<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;
use App\Models\Payment;
use App\Models\Subscription;
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

        return view('dashboard.artist.subscription', compact('subscription', 'history', 'prices'));
    }

    public function pay(Request $request)
    {
        $validated = $request->validate(['plan' => 'required|in:monthly,yearly']);
        $prices    = config('aavaan.artist_subscription');
        $plan      = $validated['plan'];
        $amount    = $plan === 'yearly' ? $prices['yearly_price'] : $prices['monthly_price'];
        $label     = $plan === 'yearly' ? 'سالانه' : 'ماهانه';

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
