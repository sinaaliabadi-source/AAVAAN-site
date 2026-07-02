<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;
use App\Models\ArtistProfile;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use Illuminate\Http\Request;

class ProductionAccessController extends Controller
{
    public function __construct(private PaymentGatewayInterface $gateway) {}

    public function index()
    {
        $user     = auth()->user();
        $accesses = $user->productionAccesses()
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->with('payment')
            ->latest()
            ->get();
        $prices = config('aavaan.production_access');

        return view('dashboard.production.access', compact('accesses', 'prices'));
    }

    public function buy(Request $request)
    {
        $validated = $request->validate(['access_type' => 'required|in:single,bundle_5,bundle_10']);
        $prices    = config('aavaan.production_access');
        $bundleMap = ['single' => 1, 'bundle_5' => 5, 'bundle_10' => 10];
        $priceMap  = [
            'single'    => $prices['single_price'],
            'bundle_5'  => $prices['bundle_5_price'],
            'bundle_10' => $prices['bundle_10_price'],
        ];
        $labelMap  = ['single' => 'تکی', 'bundle_5' => 'بسته ۵ عددی', 'bundle_10' => 'بسته ۱۰ عددی'];
        $type      = $validated['access_type'];

        $access = ProductionAccess::create([
            'user_id'     => auth()->id(),
            'access_type' => $type,
            'bundle_size' => $bundleMap[$type],
            'used_count'  => 0,
        ]);

        $payment = $access->payment()->create([
            'user_id' => auth()->id(),
            'amount'  => $priceMap[$type],
            'gateway' => config('payment.driver', 'zarinpal'),
            'status'  => 'pending',
        ]);

        try {
            $result = $this->gateway->initiate(
                $priceMap[$type],
                "خرید دسترسی آوان — {$labelMap[$type]}",
                route('production.access.callback', ['payment' => $payment->id])
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

        if ($request->query('Status') !== 'OK') {
            $payment->update(['status' => 'failed']);
            return redirect()->route('payment.failed')->with('error', 'پرداخت لغو یا ناموفق بود.');
        }

        try {
            $refId = $this->gateway->verify($payment->authority, $payment->amount);
            $payment->markPaid($refId);
            return redirect()->route('payment.success')->with([
                'ref_id'  => $refId,
                'context' => 'دسترسی تیم تولید آوان',
            ]);
        } catch (PaymentException $e) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('payment.failed')->with('error', $e->getMessage());
        }
    }

    public function unlock(Request $request)
    {
        $user      = auth()->user();
        $profileId = $request->input('artist_profile_id');
        $profile   = ArtistProfile::where('is_active', true)->findOrFail($profileId);

        if (ProductionAccessLog::where('production_user_id', $user->id)->where('artist_profile_id', $profileId)->exists()) {
            return redirect()->route('profile.show', $profile->username)->with('info', 'این هنرمند قبلاً باز شده است.');
        }

        $access = $user->availableProductionAccess();
        if (!$access) {
            return redirect()->route('production.access')->with('error', 'اعتبار دسترسی ندارید. لطفاً خرید کنید.');
        }

        ProductionAccessLog::create([
            'production_access_id' => $access->id,
            'production_user_id'   => $user->id,
            'artist_profile_id'    => $profileId,
            'accessed_at'          => now(),
        ]);
        $access->increment('used_count');

        return redirect()->route('profile.show', $profile->username)->with('success', 'دسترسی به پروفایل این هنرمند فعال شد.');
    }
}
