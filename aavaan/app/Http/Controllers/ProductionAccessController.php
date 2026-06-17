<?php

namespace App\Http\Controllers;

use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\ArtistProfile;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Exception;

class ProductionAccessController extends Controller
{
    public function __construct(private PaymentService $payment) {}

    public function index()
    {
        $user = auth()->user();
        $accesses = $user->productionAccesses()->where('payment_status', 'paid')->latest()->get();
        $prices = config('aavaan.production_access');
        return view('dashboard.production.access', compact('accesses', 'prices'));
    }

    public function buy(Request $request)
    {
        $validated = $request->validate(['access_type' => 'required|in:single,bundle_5,bundle_10']);
        $prices = config('aavaan.production_access');
        $bundleMap = ['single' => 1, 'bundle_5' => 5, 'bundle_10' => 10];
        $priceMap  = ['single' => $prices['single_price'], 'bundle_5' => $prices['bundle_5_price'], 'bundle_10' => $prices['bundle_10_price']];
        $type = $validated['access_type'];

        $access = ProductionAccess::create([
            'user_id'        => auth()->id(),
            'access_type'    => $type,
            'bundle_size'    => $bundleMap[$type],
            'amount'         => $priceMap[$type],
            'payment_status' => 'pending',
        ]);

        try {
            $result = $this->payment->request(
                $priceMap[$type],
                "خرید دسترسی آوان ({$type})",
                route('production.access.callback') . '?access=' . $access->id
            );
            $access->update(['payment_authority' => $result['authority']]);
            return redirect($result['redirect_url']);
        } catch (Exception $e) {
            $access->update(['payment_status' => 'failed']);
            return back()->with('error', 'خطا در اتصال به درگاه پرداخت: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $access = ProductionAccess::findOrFail($request->query('access'));
        if ($request->query('Status') !== 'OK') {
            $access->update(['payment_status' => 'failed']);
            return redirect()->route('production.access')->with('error', 'پرداخت لغو شد.');
        }
        try {
            $refId = $this->payment->verify($access->payment_authority, $access->amount);
            $access->update(['payment_status' => 'paid', 'payment_ref' => $refId]);
            return redirect()->route('production.search')->with('success', "پرداخت موفق. کد پیگیری: {$refId}");
        } catch (Exception $e) {
            $access->update(['payment_status' => 'failed']);
            return redirect()->route('production.access')->with('error', 'خطا در تأیید پرداخت: ' . $e->getMessage());
        }
    }

    public function unlock(Request $request)
    {
        $user = auth()->user();
        $profileId = $request->input('artist_profile_id');
        $profile = ArtistProfile::where('is_active', true)->findOrFail($profileId);

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
