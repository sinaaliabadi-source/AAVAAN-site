<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'artist')],
            'plan'          => ['required', 'in:monthly,yearly'],
            'starts_at'     => ['nullable', 'date'],
            'expires_at'    => ['nullable', 'date', 'after:starts_at'],
            'amount'        => ['nullable', 'integer', 'min:0'],
            'admin_note'    => ['nullable', 'string', 'max:1000'],
            // رفتار هنگام وجود اشتراک فعال: تمدید از انتها یا جایگزینی از امروز.
            'active_action' => ['nullable', 'in:renew,replace'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'   => 'انتخاب هنرمند الزامی است.',
            'user_id.exists'     => 'هنرمند انتخاب‌شده معتبر نیست.',
            'plan.required'      => 'انتخاب پلن الزامی است.',
            'plan.in'            => 'پلن انتخاب‌شده معتبر نیست.',
            'expires_at.after'   => 'تاریخ انقضا باید بعد از تاریخ شروع باشد.',
        ];
    }
}
