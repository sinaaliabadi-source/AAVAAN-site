<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // دسترسی ادمین از middleware گروه route تضمین می‌شود؛ اینجا فقط نقش را دوباره کنترل می‌کنیم.
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:200', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:15', 'unique:users,phone'],
            'role'          => ['required', 'in:artist,production,admin'],
            'password'      => ['required', 'confirmed', PasswordRule::min(8)],
            // تأیید دوبارهٔ ساخت ادمین — فقط وقتی نقش admin است باید پذیرفته شود.
            'admin_confirm' => ['nullable', 'accepted_if:role,admin'],
            // فیلدهای حداقلی پروفایل هنرمند
            'field'         => ['required_if:role,artist', 'nullable', 'string', 'max:100'],
            'city'          => ['nullable', 'string', 'max:100'],
            'send_welcome'  => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'نام الزامی است.',
            'name.max'               => 'نام نباید بیشتر از ۱۰۰ کاراکتر باشد.',
            'email.required'         => 'ایمیل الزامی است.',
            'email.email'            => 'فرمت ایمیل صحیح نیست.',
            'email.unique'           => 'این ایمیل قبلاً در آوان ثبت شده است.',
            'phone.unique'           => 'این شماره موبایل قبلاً ثبت شده است.',
            'phone.max'              => 'شماره موبایل نباید بیشتر از ۱۵ رقم باشد.',
            'role.required'          => 'انتخاب نقش الزامی است.',
            'role.in'                => 'نقش انتخاب‌شده معتبر نیست.',
            'password.required'      => 'رمز عبور الزامی است.',
            'password.min'           => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed'     => 'تکرار رمز عبور با رمز وارد‌شده مطابقت ندارد.',
            'admin_confirm.accepted_if' => 'برای ساخت حساب ادمین باید تیک تأیید را بزنید.',
            'field.required_if'      => 'رشتهٔ هنری برای هنرمندان الزامی است.',
        ];
    }
}
