<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ProductionApprovedMail;
use App\Mail\ProductionRejectedMail;
use App\Models\ProductionAccess;
use App\Models\ProductionAccessLog;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminProductionController extends Controller {
    use LogsAdminActivity;

    public function index(Request $request) {
        $query = User::where('role', 'production')->with('productionAccesses');

        // فیلتر وضعیت تأیید
        if (in_array($request->approval_status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('approval_status', $request->approval_status);
        }
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)->orWhere('email','like',$s));
        }

        $teams = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        // شمارندهٔ وضعیت‌ها برای تب/نشان‌ها
        $statusCounts = [
            'pending'  => User::where('role', 'production')->where('approval_status', 'pending')->count(),
            'approved' => User::where('role', 'production')->where('approval_status', 'approved')->count(),
            'rejected' => User::where('role', 'production')->where('approval_status', 'rejected')->count(),
        ];

        return view('admin.production.index', compact('teams', 'statusCounts'));
    }

    public function show(int $id) {
        $team = User::where('role', 'production')->with('approvedBy')->findOrFail($id);

        $accesses = $team->productionAccesses()->with(['payment'])->orderByDesc('created_at')->get();
        $payments = $team->payments()->with('payable')->orderByDesc('created_at')->get();
        $totalPaid = $team->payments()->where('status', 'paid')->sum('amount');
        $unlockedCount = ProductionAccessLog::where('production_user_id', $team->id)->count();

        return view('admin.production.show', compact(
            'team', 'accesses', 'payments', 'totalPaid', 'unlockedCount'
        ));
    }

    // تأیید حساب تیم تولید.
    public function approve(int $id) {
        $team = User::where('role', 'production')->findOrFail($id);

        $team->update([
            'approval_status'  => 'approved',
            'approved_at'      => now(),
            'approved_by'      => auth()->id(),
            'rejection_reason' => null,
        ]);

        $this->logAdminActivity('production_approved', "تیم تولید «{$team->name}» تأیید شد.", 'user', $team->id);

        // اطلاع‌رسانی ایمیلی — داخل try/catch تا خطای SMTP اقدام ادمین را fail نکند.
        if ($team->email) {
            try {
                Mail::to($team->email)->send(new ProductionApprovedMail($team));
            } catch (\Throwable) {
                // نادیده گرفتن خطای ارسال ایمیل
            }
        }

        return back()->with('success', 'تیم تولید تأیید شد و ایمیل اطلاع‌رسانی ارسال شد.');
    }

    // رد حساب تیم تولید همراه با دلیل الزامی.
    public function reject(Request $request, int $id) {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_reason.required' => 'ذکر دلیل رد الزامی است.',
            'rejection_reason.max'      => 'دلیل نباید بیشتر از ۱۰۰۰ کاراکتر باشد.',
        ]);

        $team = User::where('role', 'production')->findOrFail($id);

        $team->update([
            'approval_status'  => 'rejected',
            'approved_at'      => null,
            'approved_by'      => auth()->id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $this->logAdminActivity('production_rejected', "تیم تولید «{$team->name}» رد شد.", 'user', $team->id);

        if ($team->email) {
            try {
                Mail::to($team->email)->send(new ProductionRejectedMail($team, $validated['rejection_reason']));
            } catch (\Throwable) {
                // نادیده گرفتن خطای ارسال ایمیل
            }
        }

        return back()->with('success', 'حساب تیم تولید رد شد و ایمیل اطلاع‌رسانی ارسال شد.');
    }

    // افزودن اعتبار دستی به تیم تولید (فقط تیم‌های تأییدشده).
    public function addCredit(Request $request, int $id) {
        $team = User::where('role', 'production')->findOrFail($id);

        if ($team->approval_status !== 'approved') {
            return back()->with('error', 'فقط برای تیم‌های تأییدشده می‌توان اعتبار دستی افزود.');
        }

        $validated = $request->validate([
            'bundle_size' => ['required', 'integer', 'min:1', 'max:1000'],
            'expires_at'  => ['nullable', 'date', 'after:today'],
            'amount'      => ['nullable', 'integer', 'min:0'],
            'admin_note'  => ['nullable', 'string', 'max:1000'],
        ], [
            'bundle_size.required' => 'تعداد اعتبار الزامی است.',
            'bundle_size.min'      => 'تعداد اعتبار باید حداقل ۱ باشد.',
            'expires_at.after'     => 'تاریخ انقضا باید بعد از امروز باشد.',
        ]);

        // ثبت دسترسی دستی + پرداخت دستی متصل (تا گزارش‌های مالی نشکنند).
        $access = ProductionAccess::create([
            'user_id'     => $team->id,
            'access_type' => 'manual',
            'bundle_size' => $validated['bundle_size'],
            'used_count'  => 0,
            'expires_at'  => $validated['expires_at'] ?? null,
            'admin_note'  => $validated['admin_note'] ?? null,
        ]);

        $access->payment()->create([
            'user_id'        => $team->id,
            'amount'         => $validated['amount'] ?? 0,
            'gateway'        => 'manual',
            'payment_source' => 'manual',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        $this->logAdminActivity(
            'production_credit_added',
            "افزودن {$validated['bundle_size']} اعتبار دستی به تیم «{$team->name}».",
            'user',
            $team->id
        );

        return back()->with('success', "{$validated['bundle_size']} اعتبار دستی با موفقیت افزوده شد.");
    }
}
