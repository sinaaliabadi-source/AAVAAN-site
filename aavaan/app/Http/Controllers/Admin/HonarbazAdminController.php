<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\ProgramVote;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HonarbazAdminController extends Controller
{
    private function program(): Program
    {
        return Program::where('slug', config('honarbaz.program_slug'))->firstOrFail();
    }

    /** داشبورد آماری. */
    public function index()
    {
        $program = $this->program();

        $stats = [
            'total'    => $program->registrations()->count(),
            'pending'  => $program->registrations()->where('status', 'pending')->count(),
            'approved' => $program->registrations()->where('status', 'approved')->count(),
            'rejected' => $program->registrations()->where('status', 'rejected')->count(),
            'votes_verified' => $program->votes()->where('phone_verified', true)->count(),
            'votes_pending'  => $program->votes()->where('phone_verified', false)->count(),
        ];

        // نمودار ثبت‌نام روزانه ۱۴ روز اخیر.
        $since = Carbon::now()->subDays(13)->startOfDay();
        $daily = $program->registrations()
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $dailyChart = [];
        for ($i = 0; $i < 14; $i++) {
            $day = $since->copy()->addDays($i)->toDateString();
            $dailyChart[] = ['date' => $day, 'count' => (int) ($daily[$day] ?? 0)];
        }

        // توزیع استانی.
        $provinceDist = $program->registrations()
            ->selectRaw('province, COUNT(*) as c')
            ->groupBy('province')
            ->orderByDesc('c')
            ->pluck('c', 'province');

        // Top ۵ شرکت‌کننده از نظر رأی تأییدشده.
        $topContestants = $program->approvedRegistrations()
            ->withCount(['votes as votes_count' => fn($q) => $q->where('phone_verified', true)])
            ->orderByDesc('votes_count')
            ->limit(5)
            ->get();

        return view('admin.honarbaz.index', compact(
            'program', 'stats', 'dailyChart', 'provinceDist', 'topContestants'
        ));
    }

    /** جدول ثبت‌نام‌ها با فیلتر. */
    public function registrations(Request $request)
    {
        $program = $this->program();

        $query = $program->registrations()
            ->withCount(['votes as votes_count' => fn($q) => $q->where('phone_verified', true)]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('talent_type')) {
            $query->where('talent_type', $request->talent_type);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%")
                    ->orWhere('guardian_name', 'like', "%{$s}%");
            });
        }

        $registrations = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.honarbaz.registrations', [
            'program'       => $program,
            'registrations' => $registrations,
            'provinces'     => config('honarbaz.provinces'),
            'talentTypes'   => config('honarbaz.talent_types'),
            'filters'       => $request->only(['status', 'province', 'talent_type', 'search']),
        ]);
    }

    /** تغییر وضعیت یک ثبت‌نام. */
    public function updateStatus(Request $request, int $id)
    {
        $program = $this->program();

        $registration = $program->registrations()->findOrFail($id);

        $validated = $request->validate([
            'status'      => ['required', Rule::in(['approved', 'rejected', 'pending'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $registration->admin_notes,
        ]);

        return back()->with('success', 'وضعیت ثبت‌نام به‌روزرسانی شد.');
    }

    /** جدول آرا. */
    public function votes(Request $request)
    {
        $program = $this->program();

        $query = $program->votes()->with('registration');

        if ($request->filled('verified')) {
            $query->where('phone_verified', $request->verified === '1');
        }

        $votes = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.honarbaz.votes', [
            'program' => $program,
            'votes'   => $votes,
            'filters' => $request->only(['verified']),
        ]);
    }

    /** خروجی CSV از ثبت‌نام‌ها. */
    public function export(Request $request)
    {
        $program = $this->program();

        $registrations = $program->registrations()
            ->withCount(['votes as votes_count' => fn($q) => $q->where('phone_verified', true)])
            ->orderByDesc('created_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=honarbaz-registrations.csv',
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM برای اکسل
            fputcsv($file, [
                'شناسه', 'نام و نام خانوادگی', 'تلفن', 'ایمیل', 'استان', 'شهر',
                'سال تولد', 'جنسیت', 'رشته', 'توضیح استعداد', 'لینک ویدیو',
                'نام والد', 'تلفن والد', 'وضعیت', 'تعداد آرا', 'یادداشت مدیر', 'تاریخ ثبت',
            ]);
            $genderMap = ['male' => 'پسر', 'female' => 'دختر'];
            $statusMap = ['pending' => 'در انتظار', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'];
            foreach ($registrations as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->full_name,
                    $r->phone,
                    $r->email,
                    $r->province,
                    $r->city,
                    $r->birth_year,
                    $genderMap[$r->gender] ?? $r->gender,
                    $r->talent_type,
                    $r->talent_description,
                    $r->video_url,
                    $r->guardian_name,
                    $r->guardian_phone,
                    $statusMap[$r->status] ?? $r->status,
                    $r->votes_count,
                    $r->admin_notes,
                    $r->created_at?->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** فرم تنظیمات برنامه. */
    public function settings()
    {
        $program = $this->program();

        return view('admin.honarbaz.settings', compact('program'));
    }

    /** ذخیره تنظیمات برنامه. */
    public function updateSettings(Request $request)
    {
        $program = $this->program();

        $validated = $request->validate([
            'title'                => ['required', 'string', 'max:190'],
            'description'          => ['nullable', 'string'],
            'status'               => ['required', Rule::in(['draft', 'active', 'closed'])],
            'starts_at'            => ['nullable', 'date'],
            'ends_at'              => ['nullable', 'date', 'after_or_equal:starts_at'],
            'registration_enabled' => ['nullable', 'boolean'],
            'voting_enabled'       => ['nullable', 'boolean'],
        ]);

        $meta = $program->meta ?? [];
        $meta['registration_enabled'] = $request->boolean('registration_enabled');
        $meta['voting_enabled']       = $request->boolean('voting_enabled');

        $program->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'starts_at'   => $validated['starts_at'] ?? null,
            'ends_at'     => $validated['ends_at'] ?? null,
            'meta'        => $meta,
        ]);

        return back()->with('success', 'تنظیمات هنرباز ذخیره شد.');
    }
}
