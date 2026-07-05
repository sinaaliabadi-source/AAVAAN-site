<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\ArtistProfile;
use App\Models\ArtistSpecialty;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now = now();

        // ── در انتظار اقدام (مهم‌ترین‌ها) ──────────────────────────────
        $pendingProduction    = User::where('role', 'production')->where('approval_status', 'pending')->count();
        $pendingVerifications = Verification::specialty()->where('status', 'pending')->count();

        // ── مالی ───────────────────────────────────────────────────────
        // محاسبات سنگین با کش ۵ دقیقه‌ای.
        $totalRevenue = Cache::remember('admin.dash.total_revenue', 300,
            fn () => (int) Payment::where('status', 'paid')->sum('amount'));

        $revenueThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->sum('amount');
        $revenue30d = Payment::where('status', 'paid')
            ->where('paid_at', '>=', $now->copy()->subDays(30))->sum('amount');
        $paidCountThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->count();
        $creditsSold = (int) ProductionAccess::whereHas('payment', fn ($q) => $q->where('status', 'paid'))->sum('bundle_size');
        $creditsUsed = (int) ProductionAccess::whereHas('payment', fn ($q) => $q->where('status', 'paid'))->sum('used_count');

        // ── اشتراک ──────────────────────────────────────────────────────
        $activeSubscriptions = Subscription::where('status', 'active')->where('expires_at', '>', $now)->count();
        $expiringSoon = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [$now, $now->copy()->addDays(7)])->count();

        // نرخ تمدید ساده (کش‌شده): سهم اشتراک‌های این ماه که کاربرشان قبلاً اشتراک داشته.
        $renewalRate = Cache::remember('admin.dash.renewal_rate', 300, function () use ($now) {
            $base  = Subscription::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            $total = (clone $base)->count();
            if ($total === 0) return 0;
            $renewals = (clone $base)->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('subscriptions as s2')
                  ->whereColumn('s2.user_id', 'subscriptions.user_id')
                  ->whereColumn('s2.created_at', '<', 'subscriptions.created_at');
            })->count();
            return (int) round($renewals / $total * 100);
        });

        // ── محتوا ───────────────────────────────────────────────────────
        $totalSpecialties    = ArtistSpecialty::count();
        $verifiedSpecialties = Verification::specialty()->where('status', 'approved')
            ->distinct('artist_specialty_id')->count('artist_specialty_id');
        $activeProfiles   = ArtistProfile::where('is_active', true)->count();
        $inactiveProfiles = ArtistProfile::where('is_active', false)->count();

        $stats = [
            'total_users'           => User::count(),
            'total_artists'         => User::where('role', 'artist')->count(),
            'total_production'      => User::where('role', 'production')->count(),
            'new_users_today'       => User::whereDate('created_at', today())->count(),

            'pending_production'    => $pendingProduction,
            'pending_verifications' => $pendingVerifications,

            'total_revenue'         => $totalRevenue,
            'revenue_this_month'    => $revenueThisMonth,
            'revenue_30d'           => $revenue30d,
            'paid_count_month'      => $paidCountThisMonth,
            'credits_sold'          => $creditsSold,
            'credits_used'          => $creditsUsed,

            'active_subscriptions'  => $activeSubscriptions,
            'expiring_soon'         => $expiringSoon,
            'renewal_rate'          => $renewalRate,

            'total_specialties'     => $totalSpecialties,
            'verified_specialties'  => $verifiedSpecialties,
            'active_profiles'       => $activeProfiles,
            'inactive_profiles'     => $inactiveProfiles,
        ];

        // ── صف اقدامات من (حداکثر ۸ مورد ترکیبی) ───────────────────────
        $actionQueue = $this->buildActionQueue();

        // ── آخرین اقدامات ادمین (منتقل‌شده از view به کنترلر) ───────────
        $recentLogs = AdminActivityLog::with('admin')->orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'actionQueue', 'recentLogs'));
    }

    /**
     * لیست ترکیبی جدیدترین تیم‌های pending + درخواست‌های تأیید pending (حداکثر ۸).
     *
     * @return array<int, array>
     */
    private function buildActionQueue(): array
    {
        $teams = User::where('role', 'production')->where('approval_status', 'pending')
            ->latest('created_at')->limit(8)->get(['id', 'name', 'created_at'])
            ->map(fn ($u) => [
                'type'  => 'production',
                'label' => 'تأیید تیم تولید',
                'sub'   => $u->name,
                'url'   => route('admin.production.show', $u->id),
                'at'    => $u->created_at,
            ]);

        $vers = Verification::specialty()->where('status', 'pending')
            ->with(['user:id,name', 'artistSpecialty.category:id,name_fa'])
            ->latest('created_at')->limit(8)->get()
            ->map(fn ($v) => [
                'type'  => 'verification',
                'label' => 'تأیید تخصص: ' . ($v->artistSpecialty?->category?->name_fa ?? '—'),
                'sub'   => $v->user?->name ?? '—',
                'url'   => route('admin.verifications.show', $v->id),
                'at'    => $v->created_at,
            ]);

        return $teams->merge($vers)->sortByDesc('at')->take(8)->values()->all();
    }
}
