<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Mail\AccountCreatedByAdminMail;
use App\Models\ArtistProfile;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AdminUserController extends Controller {
    use LogsAdminActivity;

    // نمایش فرم افزودن کاربر. دسترسی ادمین از middleware گروه route تضمین می‌شود.
    public function create() {
        return view('admin.users.create');
    }

    // ساخت کاربر توسط ادمین (هنرمند/تیم تولید/ادمین).
    public function store(StoreUserRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'password'        => $data['password'], // cast: hashed
            'role'            => $data['role'],
            // تیم تولیدی که ادمین می‌سازد مستقیم تأییدشده است؛ سایر نقش‌ها هم approved (پیش‌فرض ستون).
            'approval_status' => 'approved',
            'approved_at'     => $data['role'] === 'production' ? now() : null,
            'approved_by'     => $data['role'] === 'production' ? auth()->id() : null,
            // کاربرِ ساخته‌شده توسط ادمین قابل‌اعتماد است و نیازی به تأیید ایمیل ندارد.
            'email_verified_at' => now(),
        ]);

        // برای هنرمند، رکورد پروفایل با username یکتای تولیدشده از نام ساخته می‌شود.
        if ($user->isArtist()) {
            ArtistProfile::create([
                'user_id'  => $user->id,
                'field'    => $data['field'],
                'city'     => $data['city'] ?? null,
                'username' => ArtistProfile::generateUniqueUsername($data['name']),
            ]);
        }

        // ارسال ایمیل خوش‌آمد حاوی رمز — داخل try/catch تا خطای SMTP اقدام ادمین را fail نکند.
        if ($request->boolean('send_welcome') && $user->email) {
            try {
                Mail::to($user->email)->send(new AccountCreatedByAdminMail($user, $data['password']));
            } catch (\Throwable) {
                // خطای ارسال ایمیل نباید ساخت کاربر را متوقف کند.
            }
        }

        $this->logAdminActivity(
            'user_created',
            "کاربر «{$user->name}» با نقش {$user->role} توسط ادمین ساخته شد.",
            'user',
            $user->id
        );

        return redirect()->route('admin.users.index')->with('success', 'کاربر جدید با موفقیت ساخته شد.');
    }

    public function index(Request $request) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $query = User::withTrashed()->with('artistProfile');
        if ($request->role) $query->where('role', $request->role);
        if ($request->status === 'banned') $query->where('is_banned', true);
        elseif ($request->status === 'active') $query->where('is_banned', false)->whereNull('deleted_at');
        elseif ($request->status === 'deleted') $query->whereNotNull('deleted_at');
        if ($request->search) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)->orWhere('email','like',$s)->orWhere('phone','like',$s));
        }
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        $users = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function edit(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $user = User::withTrashed()->findOrFail($id);
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $id,
            'phone'        => 'nullable|string|max:20',
            'role'         => 'required|in:artist,production,admin',
            'is_banned'    => 'boolean',
            'admin_notes'  => 'nullable|string|max:2000',
        ]);
        $validated['is_banned'] = $request->boolean('is_banned');
        $user->update($validated);
        $this->logAdminActivity('user_updated', "کاربر {$user->name} ویرایش شد.", 'user', $user->id);
        return back()->with('success', 'کاربر با موفقیت ویرایش شد.');
    }

    public function resetPassword(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $user = User::findOrFail($id);
        Password::sendResetLink(['email' => $user->email]);
        $this->logAdminActivity('password_reset_sent', "لینک بازنشانی رمز برای {$user->email} ارسال شد.", 'user', $user->id);
        return back()->with('success', 'لینک بازنشانی رمز عبور ارسال شد.');
    }

    public function destroy(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $user = User::findOrFail($id);
        if ($user->artistProfile) $user->artistProfile->update(['is_active' => false]);
        $user->delete();
        $this->logAdminActivity('user_deleted', "کاربر {$user->name} حذف شد.", 'user', $user->id);
        return redirect()->route('admin.users.index')->with('success', 'کاربر با موفقیت حذف شد.');
    }

    public function restore(int $id) {
        abort_unless(auth()->user()->role === 'admin', 403);
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        $this->logAdminActivity('user_restored', "کاربر {$user->name} بازیابی شد.", 'user', $user->id);
        return back()->with('success', 'کاربر بازیابی شد.');
    }
}
