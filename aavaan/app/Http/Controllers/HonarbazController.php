<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\ProgramVote;
use App\Http\Requests\StoreHonarbazRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HonarbazController extends Controller
{
    /** برنامه فعال هنرباز را برمی‌گرداند (در صورت نبودن 404). */
    private function program(): Program
    {
        return Program::where('slug', config('honarbaz.program_slug'))->firstOrFail();
    }

    /** صفحه معرفی برنامه + آمار کلی. */
    public function landing()
    {
        $program = $this->program();

        $stats = [
            'registrations' => $program->registrations()->count(),
            'approved'      => $program->approvedRegistrations()->count(),
            'provinces'     => $program->approvedRegistrations()->distinct('province')->count('province'),
            'votes'         => $program->votes()->where('phone_verified', true)->count(),
        ];

        return view('honarbaz.landing', compact('program', 'stats'));
    }

    /** فرم ثبت‌نام. */
    public function registerForm()
    {
        $program = $this->program();

        abort_unless($program->registrationOpen(), 403, 'ثبت‌نام در حال حاضر بسته است.');

        return view('honarbaz.register', [
            'program'      => $program,
            'provinces'    => config('honarbaz.provinces'),
            'talentTypes'  => config('honarbaz.talent_types'),
        ]);
    }

    /** ثبت اطلاعات ثبت‌نام. */
    public function registerSubmit(StoreHonarbazRegistrationRequest $request)
    {
        $program = $this->program();

        abort_unless($program->registrationOpen(), 403, 'ثبت‌نام در حال حاضر بسته است.');

        $data = $request->validated();

        $registration = $program->registrations()->create([
            'user_id'            => $request->user()?->id,
            'full_name'          => $data['full_name'],
            'phone'              => $data['phone'],
            'email'              => $data['email'] ?? null,
            'city'               => $data['city'],
            'province'           => $data['province'],
            'birth_year'         => $data['birth_year'],
            'gender'             => $data['gender'],
            'talent_type'        => $data['talent_type'],
            'talent_description' => $data['talent_description'],
            'video_url'          => $data['video_url'] ?? null,
            'guardian_name'      => $data['guardian_name'],
            'guardian_phone'     => $data['guardian_phone'],
            'status'             => 'pending',
        ]);

        return redirect()
            ->route('honarbaz.landing')
            ->with('honarbaz_registered', true)
            ->with('success', 'ثبت‌نام شما با موفقیت انجام شد. پس از بررسی توسط تیم هنرباز با شما تماس می‌گیریم.');
    }

    /** لیست شرکت‌کنندگان تأییدشده با فیلتر و صفحه‌بندی. */
    public function contestants(Request $request)
    {
        $program = $this->program();

        $query = $program->approvedRegistrations()
            ->withCount(['votes as votes_count' => fn($q) => $q->where('phone_verified', true)]);

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }
        if ($request->filled('talent_type')) {
            $query->where('talent_type', $request->talent_type);
        }

        $contestants = $query
            ->orderByDesc('votes_count')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('honarbaz.contestants', [
            'program'      => $program,
            'contestants'  => $contestants,
            'provinces'    => config('honarbaz.provinces'),
            'talentTypes'  => config('honarbaz.talent_types'),
            'votingOpen'   => $program->votingOpen(),
        ]);
    }

    /**
     * شروع فرآیند رأی: چک یک‌رأی‌بودن از هر IP، ذخیره رأی pending و تولید کد تأیید.
     * (فعلاً کد فقط ذخیره می‌شود — بدون SMS.)
     */
    public function vote(Request $request)
    {
        $program = $this->program();

        if (! $program->votingOpen()) {
            return response()->json(['ok' => false, 'message' => 'رأی‌گیری در حال حاضر فعال نیست.'], 422);
        }

        $validated = $request->validate([
            'registration_id' => ['required', 'integer'],
            'phone'           => ['required', 'regex:/^09[0-9]{9}$/'],
        ]);

        $registration = $program->approvedRegistrations()
            ->whereKey($validated['registration_id'])
            ->first();

        if (! $registration) {
            return response()->json(['ok' => false, 'message' => 'شرکت‌کننده یافت نشد.'], 404);
        }

        $ip = $request->ip();

        // یک رأی از هر IP برای کل برنامه (طبق UNIQUE(program_id, voter_ip)).
        $existing = ProgramVote::where('program_id', $program->id)
            ->where('voter_ip', $ip)
            ->first();

        if ($existing && $existing->phone_verified) {
            return response()->json(['ok' => false, 'message' => 'شما قبلاً از این دستگاه رأی داده‌اید.'], 429);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes((int) config('honarbaz.verification_code_expiry_minutes', 10));

        $vote = ProgramVote::updateOrCreate(
            ['program_id' => $program->id, 'voter_ip' => $ip],
            [
                'registration_id'  => $registration->id,
                'voter_phone'      => $validated['phone'],
                'phone_verified'   => false,
                'verification_code' => $code,
                'code_expires_at'  => $expiresAt,
            ]
        );

        // در نبود SMS، کد در session نگهداری می‌شود تا فرآیند قابل تست باشد.
        $request->session()->put('honarbaz_vote_code', $code);

        return response()->json([
            'ok'      => true,
            'message' => 'کد تأیید ارسال شد. لطفاً کد را وارد کنید.',
            'vote_id' => $vote->id,
            // فقط در محیط توسعه کد بازگردانده می‌شود تا تست آسان باشد.
            'debug_code' => app()->environment('local', 'testing') ? $code : null,
        ]);
    }

    /** تأیید کد و نهایی‌کردن رأی. */
    public function verifyVote(Request $request)
    {
        $program = $this->program();

        $validated = $request->validate([
            'vote_id' => ['required', 'integer'],
            'code'    => ['required', 'digits:6'],
        ]);

        $vote = ProgramVote::where('program_id', $program->id)
            ->where('voter_ip', $request->ip())
            ->whereKey($validated['vote_id'])
            ->first();

        if (! $vote) {
            return response()->json(['ok' => false, 'message' => 'رأی یافت نشد.'], 404);
        }

        if ($vote->phone_verified) {
            return response()->json(['ok' => false, 'message' => 'این رأی قبلاً تأیید شده است.'], 422);
        }

        if ($vote->codeExpired()) {
            return response()->json(['ok' => false, 'message' => 'کد تأیید منقضی شده است. دوباره تلاش کنید.'], 422);
        }

        if (! hash_equals((string) $vote->verification_code, (string) $validated['code'])) {
            return response()->json(['ok' => false, 'message' => 'کد وارد‌شده صحیح نیست.'], 422);
        }

        $vote->update([
            'phone_verified'   => true,
            'verification_code' => null,
            'code_expires_at'  => null,
        ]);

        $request->session()->forget('honarbaz_vote_code');

        $count = $program->votes()
            ->where('registration_id', $vote->registration_id)
            ->where('phone_verified', true)
            ->count();

        return response()->json([
            'ok'          => true,
            'message'     => 'رأی شما با موفقیت ثبت شد. سپاسگزاریم!',
            'votes_count' => $count,
        ]);
    }
}
