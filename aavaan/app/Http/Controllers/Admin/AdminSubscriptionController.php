<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManualSubscriptionRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminSubscriptionController extends Controller {
    use LogsAdminActivity;

    // فرم ساخت اشتراک دستی.
    public function create() {
        return view('admin.subscriptions.create');
    }

    // endpoint JSON برای autocomplete انتخاب هنرمند (نام یا ایمیل). حداکثر ۱۰ نتیجه.
    public function artistSearch(Request $request) {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $like = '%' . $q . '%';
        $results = User::where('role', 'artist')
            ->where(fn($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like))
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                // آیا اشتراک فعال دارد؟ برای نمایش گزینهٔ تمدید/جایگزینی در فرم.
                'has_active' => $u->hasActiveSubscription(),
            ]);

        return response()->json($results);
    }

    // ثبت اشتراک دستی برای هنرمند + پرداخت دستی متصل.
    public function store(StoreManualSubscriptionRequest $request) {
        $data   = $request->validated();
        $artist = User::where('role', 'artist')->findOrFail($data['user_id']);

        $plan      = $data['plan'];
        $active    = $artist->activeSubscription();
        $action    = $data['active_action'] ?? 'replace';

        // تعیین تاریخ شروع بر اساس رفتار انتخابی هنگام وجود اشتراک فعال.
        if ($active && $action === 'renew') {
            // تمدید از انتهای اشتراک فعلی؛ اشتراک فعلی دست‌نخورده می‌ماند.
            $startsAt = $active->expires_at && $active->expires_at->isFuture()
                ? $active->expires_at->copy()
                : now();
        } else {
            $startsAt = !empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : now();
            // جایگزینی از امروز: اشتراک فعال قبلی منقضی می‌شود.
            if ($active && $action === 'replace') {
                $active->update(['status' => 'expired']);
            }
        }

        // انقضا: از ورودی اگر داده شده، وگرنه محاسبهٔ خودکار از پلن.
        $expiresAt = !empty($data['expires_at'])
            ? Carbon::parse($data['expires_at'])
            : ($plan === 'yearly' ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth());

        $subscription = Subscription::create([
            'user_id'    => $artist->id,
            'plan'       => $plan,
            'status'     => 'active',
            'starts_at'  => $startsAt,
            'expires_at' => $expiresAt,
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        // پرداخت دستی متصل (status=paid, source=manual) تا گزارش‌های مالی نشکنند.
        $subscription->payment()->create([
            'user_id'        => $artist->id,
            'amount'         => $data['amount'] ?? 0,
            'gateway'        => 'manual',
            'payment_source' => 'manual',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        $this->logAdminActivity(
            'subscription_created_manual',
            "اشتراک دستی {$plan} برای «{$artist->name}» ثبت شد (اقدام: {$action}).",
            'subscription',
            $subscription->id
        );

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'اشتراک دستی با موفقیت ثبت شد.');
    }

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = Subscription::with(['user', 'payment']);
        if ($request->status === 'active') $query->where('status', 'active')->where('expires_at', '>', now());
        elseif ($request->status === 'expired') $query->where(fn($q) => $q->where('status', 'expired')->orWhere('expires_at', '<=', now()));
        elseif ($request->status) $query->where('status', $request->status);
        if ($request->plan) $query->where('plan', $request->plan);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->whereHas('user', fn($q) => $q->where('name','like',$s)->orWhere('email','like',$s));
        }
        $subscriptions = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $subscription = Subscription::with(['user', 'payment'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => 'expired']);
        $this->logAdminActivity('subscription_cancelled', "اشتراک #{$id} لغو شد.", 'subscription', $id);
        return back()->with('success', 'اشتراک لغو شد.');
    }

    public function extend(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $request->validate(['expires_at' => 'required|date|after:today']);
        $sub = Subscription::findOrFail($id);
        $sub->update(['status' => 'active', 'expires_at' => $request->expires_at]);
        $this->logAdminActivity('subscription_extended', "اشتراک #{$id} تمدید شد تا {$request->expires_at}.", 'subscription', $id);
        return back()->with('success', 'اشتراک تمدید شد.');
    }
}
