<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHonarbazRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'          => ['required', 'string', 'max:100'],
            'phone'              => ['required', 'regex:/^09[0-9]{9}$/'],
            'email'              => ['nullable', 'email', 'max:190'],
            'birth_year'         => ['required', 'integer', 'between:1380,1410'],
            'gender'             => ['required', Rule::in(['male', 'female'])],
            'province'           => ['required', 'string', Rule::in(config('honarbaz.provinces'))],
            'city'               => ['required', 'string', 'max:100'],
            'talent_type'        => ['required', 'string', Rule::in(config('honarbaz.talent_types'))],
            'talent_description' => ['required', 'string', 'min:50', 'max:1000'],
            'video_url'          => ['nullable', 'regex:/^https:\/\/(www\.)?aparat\.com\/v\/[A-Za-z0-9]+/'],
            'guardian_name'      => ['required', 'string', 'max:100'],
            'guardian_phone'     => ['required', 'regex:/^09[0-9]{9}$/'],
            'terms'              => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'          => 'وارد کردن نام و نام خانوادگی الزامی است.',
            'phone.required'              => 'شماره تلفن الزامی است.',
            'phone.regex'                 => 'شماره تلفن باید با ۰۹ شروع شود و ۱۱ رقم باشد.',
            'birth_year.required'         => 'سال تولد الزامی است.',
            'birth_year.between'          => 'برنامه ویژه کودکان و نوجوانان است؛ سال تولد باید بین ۱۳۸۰ تا ۱۴۱۰ باشد.',
            'gender.required'             => 'انتخاب جنسیت الزامی است.',
            'province.required'           => 'انتخاب استان الزامی است.',
            'province.in'                 => 'استان انتخاب‌شده معتبر نیست.',
            'city.required'               => 'وارد کردن شهر الزامی است.',
            'talent_type.required'        => 'انتخاب رشته هنری الزامی است.',
            'talent_type.in'              => 'رشته هنری انتخاب‌شده معتبر نیست.',
            'talent_description.required' => 'توضیح استعداد الزامی است.',
            'talent_description.min'      => 'توضیح استعداد باید حداقل ۵۰ کاراکتر باشد.',
            'video_url.regex'             => 'لینک ویدیو باید یک آدرس معتبر آپارات باشد (مثال: https://aparat.com/v/abc123).',
            'guardian_name.required'      => 'وارد کردن نام والد/سرپرست الزامی است.',
            'guardian_phone.required'     => 'شماره تلفن والد الزامی است.',
            'guardian_phone.regex'        => 'شماره تلفن والد باید با ۰۹ شروع شود و ۱۱ رقم باشد.',
            'terms.required'              => 'پذیرش قوانین الزامی است.',
            'terms.accepted'             => 'برای ثبت‌نام باید قوانین را بپذیرید.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // نرمال‌سازی ارقام فارسی/عربی به لاتین برای فیلدهای عددی
        $this->merge([
            'phone'          => $this->normalizeDigits($this->input('phone')),
            'guardian_phone' => $this->normalizeDigits($this->input('guardian_phone')),
            'birth_year'     => $this->normalizeDigits($this->input('birth_year')),
        ]);
    }

    private function normalizeDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $ar = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace(array_merge($fa, $ar), array_merge($en, $en), $value);
    }
}
