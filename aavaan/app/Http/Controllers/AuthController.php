<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ArtistProfile;
use App\Models\SpecialtyCategory;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.index', ['fields' => SpecialtyCategory::fieldOptions()]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|max:200',
            'password' => 'required|string',
        ], [
            'email.required'    => 'ایمیل یا شماره موبایل الزامی است.',
            'password.required' => 'رمز عبور الزامی است.',
        ]);

        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'ایمیل/شماره موبایل یا رمز عبور اشتباه است.'])->withInput();
        }

        $request->session()->regenerate();
        return $this->redirectToDashboard();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'nullable|email|max:200|unique:users,email',
            'phone'    => 'nullable|string|max:15|unique:users,phone',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'role'     => 'required|in:artist,production',
            'field'    => 'required_if:role,artist|nullable|string|max:100',
        ], [
            'name.required'      => 'نام الزامی است.',
            'name.max'           => 'نام نباید بیشتر از ۱۰۰ کاراکتر باشد.',
            'email.email'        => 'فرمت ایمیل صحیح نیست.',
            'email.unique'       => 'این ایمیل قبلاً در آوان ثبت شده است.',
            'phone.unique'       => 'این شماره موبایل قبلاً در آوان ثبت شده است.',
            'phone.max'          => 'شماره موبایل نباید بیشتر از ۱۵ رقم باشد.',
            'password.required'  => 'رمز عبور الزامی است.',
            'password.min'       => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور با رمز وارد‌شده مطابقت ندارد.',
            'role.required'      => 'لطفاً نوع حساب خود را انتخاب کنید.',
            'role.in'            => 'نوع حساب انتخاب‌شده معتبر نیست.',
            'field.required_if'  => 'رشته هنری برای هنرمندان الزامی است.',
        ]);

        if (empty($validated['email']) && empty($validated['phone'])) {
            return back()
                ->withErrors(['email' => 'حداقل یکی از ایمیل یا شماره موبایل الزامی است.'])
                ->withInput();
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'] ?? null,
            'phone'    => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role'     => $validated['role'],
            // تیم‌های تولید تا تأیید ادمین در وضعیت pending می‌مانند؛ هنرمندان همیشه approved
            // (پیش‌فرض ستون). این تمایز، دسترسی تیم تولید تأییدنشده را مسدود می‌کند.
            'approval_status' => $validated['role'] === 'production' ? 'pending' : 'approved',
        ]);

        if ($user->isArtist()) {
            ArtistProfile::create([
                'user_id' => $user->id,
                'field'   => $validated['field'],
            ]);

            // جشنوارهٔ افتتاح: اشتراک رایگان خودکار برای هنرمند تازه‌ثبت‌نام‌کرده.
            \App\Support\Festival::grantSubscription($user);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Send welcome email (silently — don't fail registration if mail fails)
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Throwable) {
                // Mail failure must never break registration
            }
        }

        if ($user->isArtist()) {
            return redirect()->route('artist.profile')
                ->with('success', 'خوش آمدید! پروفایل خود را تکمیل کنید تا در نتایج جستجو ظاهر شوید.');
        }

        // تیم تولید پس از ثبت‌نام به صفحهٔ انتظار تأیید می‌رود، نه داشبورد.
        return redirect()->route('production.pending-approval')
            ->with('success', 'ثبت‌نام شما انجام شد و در انتظار تأیید تیم آوان است.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function forgotForm()
    {
        return view('auth.forgot');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        // Laravel 10: compare against the string value, not a class constant
        return $status === 'passwords.sent'
            ? back()->with('success', 'لینک بازیابی رمز به ایمیل شما ارسال شد.')
            : back()->withErrors(['email' => 'آدرس ایمیل یافت نشد.']);
    }

    public function resetForm(string $token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
                Auth::login($user);
            }
        );

        // Laravel 10: compare against the string value, not a class constant
        return $status === 'passwords.reset'
            ? redirect()->route('home')->with('success', 'رمز عبور با موفقیت تغییر کرد.')
            : back()->withErrors(['email' => __($status)]);
    }

    private function redirectToDashboard()
    {
        return match (Auth::user()->role) {
            'artist'     => redirect()->route('artist.dashboard'),
            'production' => redirect()->route('production.dashboard'),
            'admin'      => redirect('/admin'),
            default      => redirect()->route('home'),
        };
    }
}
