<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\JalaliHelper;
use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\DiscountCodeUse;
use App\Models\Payment;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    //  Dashboard
    // ──────────────────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->toDateString()))->endOfDay();

        $daysDiff  = max(1, (int) $from->diffInDays($to));
        $prevEnd   = $from->copy()->subSecond();
        $prevStart = $prevEnd->copy()->subDays($daysDiff);

        $subRevenue  = $this->sumRevenue('subscription', $from, $to);
        $prodRevenue = $this->sumRevenue('production',   $from, $to);
        $totalRevenue = $subRevenue + $prodRevenue;

        $prevSubRevenue  = $this->sumRevenue('subscription', $prevStart, $prevEnd);
        $prevProdRevenue = $this->sumRevenue('production',   $prevStart, $prevEnd);
        $prevTotalRevenue = $prevSubRevenue + $prevProdRevenue;

        $newUsers     = User::whereBetween('created_at', [$from, $to])->count();
        $prevNewUsers = User::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $monthlyRevenue = $this->getMonthlyRevenue();

        return view('admin.reports.index', compact(
            'from', 'to', 'daysDiff',
            'totalRevenue', 'subRevenue', 'prodRevenue',
            'prevTotalRevenue', 'prevSubRevenue', 'prevProdRevenue',
            'newUsers', 'prevNewUsers',
            'monthlyRevenue'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Chart JSON API
    // ──────────────────────────────────────────────────────────────────────────

    public function chartData(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date',
            'type' => 'nullable|in:revenue,doughnut,users',
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->toDateString()))->endOfDay();
        $type = $request->input('type', 'revenue');

        $cacheKey = 'reports.chart.' . md5("{$type}|{$from->timestamp}|{$to->timestamp}");

        $data = Cache::remember($cacheKey, 3600, function () use ($from, $to, $type) {
            if ($type === 'doughnut') {
                return [
                    'subscription' => $this->sumRevenue('subscription', $from, $to),
                    'production'   => $this->sumRevenue('production',   $from, $to),
                ];
            }
            if ($type === 'users') {
                return $this->buildUsersChartData($from, $to);
            }
            return $this->buildRevenueChartData($from, $to);
        });

        return response()->json($data);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Subscriptions report
    // ──────────────────────────────────────────────────────────────────────────

    public function subscriptions(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'plan'   => 'nullable|in:monthly,yearly',
            'status' => 'nullable|in:active,expired,pending',
        ]);

        $from   = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to     = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : null;

        $query = Subscription::with(['user', 'payment', 'discountUse.discountCode'])
            ->when($from,             fn ($q) => $q->where('starts_at', '>=', $from))
            ->when($to,               fn ($q) => $q->where('starts_at', '<=', $to))
            ->when($request->plan,    fn ($q) => $q->where('plan',   $request->plan))
            ->when($request->status,  fn ($q) => $q->where('status', $request->status));

        $subscriptions = $query->orderByDesc('created_at')->paginate(50)->withQueryString();

        // Stats (same filters, only paid)
        $statsBase = Subscription::whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->when($from,            fn ($q) => $q->where('starts_at', '>=', $from))
            ->when($to,              fn ($q) => $q->where('starts_at', '<=', $to))
            ->when($request->plan,   fn ($q) => $q->where('plan',   $request->plan))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        $allStats     = (clone $statsBase)->with('payment')->get();
        $totalRevenue = $allStats->sum(fn ($s) => $s->payment?->amount ?? 0);
        $monthlyCount = $allStats->where('plan', 'monthly')->count();
        $yearlyCount  = $allStats->where('plan', 'yearly')->count();

        // Avg duration (days)
        $durRows = (clone $statsBase)
            ->whereNotNull('starts_at')
            ->whereNotNull('expires_at')
            ->selectRaw('AVG(DATEDIFF(expires_at, starts_at)) as avg_days')
            ->value('avg_days');
        $avgDays = round((float)$durRows);

        // Renewal rate
        $uniqueUsers  = $allStats->pluck('user_id')->unique()->count();
        $renewedCount = Subscription::select('user_id')
            ->whereIn('user_id', $allStats->pluck('user_id')->unique())
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()->count();
        $renewalRate = $uniqueUsers > 0 ? round($renewedCount / $uniqueUsers * 100, 1) : 0;

        return view('admin.reports.subscriptions', compact(
            'subscriptions', 'totalRevenue', 'monthlyCount', 'yearlyCount',
            'avgDays', 'renewalRate'
        ));
    }

    public function exportSubscriptions(Request $request): Response
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'plan'   => 'nullable|in:monthly,yearly',
            'status' => 'nullable|in:active,expired,pending',
        ]);

        $from  = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to    = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : null;

        $rows = Subscription::with(['user', 'payment', 'discountUse.discountCode'])
            ->when($from,            fn ($q) => $q->where('starts_at', '>=', $from))
            ->when($to,              fn ($q) => $q->where('starts_at', '<=', $to))
            ->when($request->plan,   fn ($q) => $q->where('plan',   $request->plan))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename=subscriptions.csv'];

        $callback = function () use ($rows) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['#', 'نام هنرمند', 'ایمیل', 'نوع پلن', 'مبلغ (تومان)', 'کد تخفیف', 'شروع', 'پایان', 'وضعیت', 'تاریخ پرداخت']);
            foreach ($rows as $s) {
                $plan = $s->plan === 'monthly' ? 'ماهانه' : 'سالانه';
                $statusMap = ['active' => 'فعال', 'expired' => 'منقضی', 'pending' => 'در انتظار'];
                fputcsv($f, [
                    $s->id,
                    $s->user?->name,
                    $s->user?->email,
                    $plan,
                    $s->payment?->amount ?? 0,
                    $s->discountUse?->discountCode?->code ?? '—',
                    $s->starts_at?->format('Y-m-d H:i'),
                    $s->expires_at?->format('Y-m-d H:i'),
                    $statusMap[$s->status] ?? $s->status,
                    $s->payment?->paid_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Production access report
    // ──────────────────────────────────────────────────────────────────────────

    public function productionAccess(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $from   = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to     = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : null;
        $status = $request->input('status', 'paid');

        $query = ProductionAccess::with(['user', 'payment'])
            ->whereHas('payment', function ($q) use ($from, $to, $status) {
                if ($status) $q->where('status', $status);
                if ($from)   $q->where('paid_at', '>=', $from);
                if ($to)     $q->where('paid_at', '<=', $to);
            });

        $accesses = $query->orderByDesc('created_at')->paginate(50)->withQueryString();

        $totalRevenue = Payment::where('payable_type', 'App\\Models\\ProductionAccess')
            ->where('status', 'paid')
            ->when($from, fn ($q) => $q->where('paid_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('paid_at', '<=', $to))
            ->sum('amount');

        $topProducers = ProductionAccess::select('user_id', DB::raw('COUNT(*) as access_count'))
            ->whereHas('payment', function ($q) use ($from, $to) {
                $q->where('status', 'paid');
                if ($from) $q->where('paid_at', '>=', $from);
                if ($to)   $q->where('paid_at', '<=', $to);
            })
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('access_count')
            ->limit(5)
            ->get();

        $topArtists = ProductionAccessLog::select('artist_profile_id', DB::raw('COUNT(*) as access_count'))
            ->with('artistProfile.user')
            ->groupBy('artist_profile_id')
            ->orderByDesc('access_count')
            ->limit(5)
            ->get();

        return view('admin.reports.production_access', compact(
            'accesses', 'totalRevenue', 'topProducers', 'topArtists', 'status'
        ));
    }

    public function exportProductionAccess(Request $request): Response
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $from   = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to     = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : null;
        $status = $request->input('status', 'paid');

        $rows = ProductionAccess::with(['user', 'payment'])
            ->whereHas('payment', function ($q) use ($from, $to, $status) {
                if ($status) $q->where('status', $status);
                if ($from)   $q->where('paid_at', '>=', $from);
                if ($to)     $q->where('paid_at', '<=', $to);
            })
            ->orderByDesc('created_at')
            ->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename=production-access.csv'];

        $callback = function () use ($rows) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['#', 'نام تیم تولید', 'ایمیل', 'نوع دسترسی', 'تعداد بسته', 'مبلغ (تومان)', 'وضعیت پرداخت', 'تاریخ پرداخت']);
            $typeMap = ['single' => 'تکی', 'bundle_5' => 'بسته ۵تایی', 'bundle_10' => 'بسته ۱۰تایی'];
            $stMap   = ['paid' => 'پرداخت‌شده', 'pending' => 'در انتظار', 'failed' => 'ناموفق', 'refunded' => 'بازگشت'];
            foreach ($rows as $a) {
                fputcsv($f, [
                    $a->id,
                    $a->user?->name,
                    $a->user?->email,
                    $typeMap[$a->access_type] ?? $a->access_type,
                    $a->bundle_size,
                    $a->payment?->amount ?? 0,
                    $stMap[$a->payment?->status] ?? '—',
                    $a->payment?->paid_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Discounts report
    // ──────────────────────────────────────────────────────────────────────────

    public function discounts(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $discounts = DiscountCode::withCount('uses')
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        // Total discount given per code
        $totals = DiscountCodeUse::select('discount_code_id', DB::raw('COUNT(*) as use_count'))
            ->groupBy('discount_code_id')
            ->pluck('use_count', 'discount_code_id');

        return view('admin.reports.discounts', compact('discounts', 'totals'));
    }

    public function discountShow(int $id)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $discount = DiscountCode::findOrFail($id);
        $uses = DiscountCodeUse::with(['user', 'subscription.payment'])
            ->where('discount_code_id', $id)
            ->orderByDesc('used_at')
            ->paginate(50);

        return view('admin.reports.discount_show', compact('discount', 'uses'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Users growth report
    // ──────────────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->toDateString()))->endOfDay();

        // New users list in range
        $newUsers = User::with('artistProfile')
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        // Monthly table (last 12 months)
        $monthlyUsers = $this->getMonthlyUsers();

        return view('admin.reports.users', compact('from', 'to', 'newUsers', 'monthlyUsers'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Global export page
    // ──────────────────────────────────────────────────────────────────────────

    public function exportPage()
    {
        abort_unless(auth()->user()->role === 'admin', 403);
        return view('admin.reports.export');
    }

    public function exportDownload(Request $request): Response
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $request->validate([
            'type' => 'required|in:payments,subscriptions,production,users',
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to   = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : null;

        $type = $request->input('type');

        switch ($type) {
            case 'payments':
                return $this->downloadPayments($from, $to);
            case 'subscriptions':
                return $this->downloadSubsExport($from, $to);
            case 'production':
                return $this->downloadProdExport($from, $to);
            case 'users':
                return $this->downloadUsersExport($from, $to);
        }

        abort(400);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function sumRevenue(string $type, Carbon $from, Carbon $to): int
    {
        $morphType = $type === 'subscription'
            ? 'App\\Models\\Subscription'
            : 'App\\Models\\ProductionAccess';

        return (int) Payment::where('payable_type', $morphType)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');
    }

    private function getMonthlyRevenue(): array
    {
        $start = now()->startOfMonth()->subMonths(11)->startOfDay();
        $end   = now()->endOfMonth()->endOfDay();

        $subData = Payment::select(
                DB::raw('YEAR(paid_at) as yr'),
                DB::raw('MONTH(paid_at) as mo'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('payable_type', 'App\\Models\\Subscription')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $prodData = Payment::select(
                DB::raw('YEAR(paid_at) as yr'),
                DB::raw('MONTH(paid_at) as mo'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('payable_type', 'App\\Models\\ProductionAccess')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);
            $key  = $date->year . '-' . $date->month;
            $months[] = [
                'date'         => $date,
                'sub_revenue'  => (int)($subData->get($key)?->total  ?? 0),
                'prod_revenue' => (int)($prodData->get($key)?->total ?? 0),
                'total'        => (int)(($subData->get($key)?->total ?? 0) + ($prodData->get($key)?->total ?? 0)),
                'sub_count'    => (int)($subData->get($key)?->cnt   ?? 0),
                'prod_count'   => (int)($prodData->get($key)?->cnt  ?? 0),
            ];
        }

        return $months;
    }

    private function getMonthlyUsers(): array
    {
        $start = now()->startOfMonth()->subMonths(11)->startOfDay();
        $end   = now()->endOfMonth()->endOfDay();

        $artistData = User::select(
                DB::raw('YEAR(created_at) as yr'),
                DB::raw('MONTH(created_at) as mo'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('role', 'artist')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $prodData = User::select(
                DB::raw('YEAR(created_at) as yr'),
                DB::raw('MONTH(created_at) as mo'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('role', 'production')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn ($r) => $r->yr . '-' . $r->mo);

        $months = [];
        $prevTotal = 0;
        for ($i = 11; $i >= 0; $i--) {
            $date     = now()->startOfMonth()->subMonths($i);
            $key      = $date->year . '-' . $date->month;
            $artists  = (int)($artistData->get($key)?->cnt ?? 0);
            $prod     = (int)($prodData->get($key)?->cnt  ?? 0);
            $total    = $artists + $prod;
            $growth   = $prevTotal > 0 ? round(($total - $prevTotal) / $prevTotal * 100, 1) : null;
            $months[] = [
                'date'         => $date,
                'artists'      => $artists,
                'production'   => $prod,
                'total'        => $total,
                'growth'       => $growth,
            ];
            $prevTotal = $total;
        }

        return $months;
    }

    private function buildRevenueChartData(Carbon $from, Carbon $to): array
    {
        $daysDiff  = max(1, (int) $from->diffInDays($to));
        $useWeekly = $daysDiff > 60;

        if ($useWeekly) {
            $subData = Payment::select(
                    DB::raw('YEARWEEK(paid_at, 1) as wk'),
                    DB::raw('MIN(DATE(paid_at)) as wk_start'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('payable_type', 'App\\Models\\Subscription')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('wk')->orderBy('wk')->get()->keyBy('wk');

            $prodData = Payment::select(
                    DB::raw('YEARWEEK(paid_at, 1) as wk'),
                    DB::raw('MIN(DATE(paid_at)) as wk_start'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('payable_type', 'App\\Models\\ProductionAccess')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('wk')->orderBy('wk')->get()->keyBy('wk');

            $allKeys   = $subData->keys()->merge($prodData->keys())->unique()->sort()->values();
            $labels    = $subValues = $prodValues = [];
            foreach ($allKeys as $wk) {
                $ws       = $subData->get($wk)?->wk_start ?? $prodData->get($wk)?->wk_start;
                $labels[] = $ws ? JalaliHelper::toDate(Carbon::parse($ws)) : (string) $wk;
                $subValues[]  = (int)($subData->get($wk)?->total  ?? 0);
                $prodValues[] = (int)($prodData->get($wk)?->total ?? 0);
            }
        } else {
            $subMap = Payment::select(DB::raw('DATE(paid_at) as day'), DB::raw('SUM(amount) as total'))
                ->where('payable_type', 'App\\Models\\Subscription')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('day')->get()->keyBy('day');

            $prodMap = Payment::select(DB::raw('DATE(paid_at) as day'), DB::raw('SUM(amount) as total'))
                ->where('payable_type', 'App\\Models\\ProductionAccess')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$from, $to])
                ->groupBy('day')->get()->keyBy('day');

            $labels    = $subValues = $prodValues = [];
            $cur = $from->copy()->startOfDay();
            $end = $to->copy()->startOfDay();
            while ($cur->lte($end)) {
                $dk = $cur->toDateString();
                $labels[]     = JalaliHelper::toDate($cur);
                $subValues[]  = (int)($subMap->get($dk)?->total  ?? 0);
                $prodValues[] = (int)($prodMap->get($dk)?->total ?? 0);
                $cur->addDay();
            }
        }

        return [
            'labels'       => $labels,
            'subscription' => $subValues,
            'production'   => $prodValues,
            'weekly'       => $useWeekly,
        ];
    }

    private function buildUsersChartData(Carbon $from, Carbon $to): array
    {
        $artistMap = User::select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->where('role', 'artist')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')->get()->keyBy('day');

        $prodMap = User::select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->where('role', 'production')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')->get()->keyBy('day');

        $labels = $artistValues = $prodValues = [];
        $cur = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        while ($cur->lte($end)) {
            $dk = $cur->toDateString();
            $labels[]       = JalaliHelper::toDate($cur);
            $artistValues[] = (int)($artistMap->get($dk)?->total ?? 0);
            $prodValues[]   = (int)($prodMap->get($dk)?->total   ?? 0);
            $cur->addDay();
        }

        return ['labels' => $labels, 'artists' => $artistValues, 'production' => $prodValues];
    }

    // ── Global export helpers ────────────────────────────────────────────────

    private function downloadPayments(?Carbon $from, ?Carbon $to): Response
    {
        $rows = Payment::with('user')
            ->where('status', 'paid')
            ->when($from, fn ($q) => $q->where('paid_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('paid_at', '<=', $to))
            ->orderByDesc('paid_at')->get();

        return $this->streamCsv('payments.csv', ['#','کاربر','ایمیل','مبلغ (تومان)','نوع','کد مرجع','تاریخ پرداخت'],
            $rows, function ($f, $p) {
                $type = str_contains($p->payable_type, 'Subscription') ? 'اشتراک' : 'دسترسی تولید';
                fputcsv($f, [$p->id, $p->user?->name, $p->user?->email, $p->amount, $type, $p->ref_id, $p->paid_at?->format('Y-m-d H:i')]);
            });
    }

    private function downloadSubsExport(?Carbon $from, ?Carbon $to): Response
    {
        $rows = Subscription::with(['user','payment','discountUse.discountCode'])
            ->when($from, fn ($q) => $q->where('starts_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('starts_at', '<=', $to))
            ->orderByDesc('created_at')->get();

        return $this->streamCsv('subscriptions.csv', ['#','نام','ایمیل','پلن','مبلغ','کد تخفیف','شروع','پایان','وضعیت','تاریخ پرداخت'],
            $rows, function ($f, $s) {
                fputcsv($f, [
                    $s->id, $s->user?->name, $s->user?->email,
                    $s->plan === 'monthly' ? 'ماهانه' : 'سالانه',
                    $s->payment?->amount ?? 0,
                    $s->discountUse?->discountCode?->code ?? '—',
                    $s->starts_at?->format('Y-m-d H:i'),
                    $s->expires_at?->format('Y-m-d H:i'),
                    $s->status,
                    $s->payment?->paid_at?->format('Y-m-d H:i'),
                ]);
            });
    }

    private function downloadProdExport(?Carbon $from, ?Carbon $to): Response
    {
        $rows = ProductionAccess::with(['user','payment'])
            ->whereHas('payment', function ($q) use ($from, $to) {
                $q->where('status', 'paid');
                if ($from) $q->where('paid_at', '>=', $from);
                if ($to)   $q->where('paid_at', '<=', $to);
            })
            ->orderByDesc('created_at')->get();

        $typeMap = ['single' => 'تکی', 'bundle_5' => 'بسته ۵تایی', 'bundle_10' => 'بسته ۱۰تایی'];

        return $this->streamCsv('production-access.csv', ['#','نام تیم','ایمیل','نوع','مبلغ','تاریخ پرداخت'],
            $rows, function ($f, $a) use ($typeMap) {
                fputcsv($f, [
                    $a->id, $a->user?->name, $a->user?->email,
                    $typeMap[$a->access_type] ?? $a->access_type,
                    $a->payment?->amount ?? 0,
                    $a->payment?->paid_at?->format('Y-m-d H:i'),
                ]);
            });
    }

    private function downloadUsersExport(?Carbon $from, ?Carbon $to): Response
    {
        $rows = User::with('artistProfile')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to))
            ->orderByDesc('created_at')->get();

        $roleMap = ['artist' => 'هنرمند', 'production' => 'تیم تولید', 'admin' => 'مدیر'];

        return $this->streamCsv('users.csv', ['#','نام','ایمیل','نقش','تاریخ ثبت‌نام'],
            $rows, function ($f, $u) use ($roleMap) {
                fputcsv($f, [$u->id, $u->name, $u->email, $roleMap[$u->role] ?? $u->role, $u->created_at?->format('Y-m-d H:i')]);
            });
    }

    private function streamCsv(string $filename, array $headers, $rows, callable $rowFn): Response
    {
        $hdrs = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => "attachment; filename={$filename}"];
        $callback = function () use ($headers, $rows, $rowFn) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, $headers);
            foreach ($rows as $row) {
                $rowFn($f, $row);
            }
            fclose($f);
        };
        return response()->stream($callback, 200, $hdrs);
    }

    private function pctChange(int|float $current, int|float $prev): ?float
    {
        if ($prev == 0) return null;
        return round(($current - $prev) / $prev * 100, 1);
    }
}
