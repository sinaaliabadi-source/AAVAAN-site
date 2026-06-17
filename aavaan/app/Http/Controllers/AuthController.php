<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ArtistProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|max:200',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'ایمیل/شماره یا رمز عبور اشتباه است.'])->withInput();
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
        ]);

        if (empty($validated['email']) && empty($validated['phone'])) {
            return back()->withErrors(['email' => 'ایمیل یا شماره موبایل الزامی است.'])->withInput();
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'] ?? null,
            'phone'    => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role'     => $validated['role'],
        ]);

        if ($user->isArtist()) {
            ArtistProfile::create([
                'user_id' => $user->id,
                'field'   => $validated['field'],
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectToDashboard();
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
        return $status === Password::ResetLinkSent
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

        return $status === Password::PasswordReset
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
