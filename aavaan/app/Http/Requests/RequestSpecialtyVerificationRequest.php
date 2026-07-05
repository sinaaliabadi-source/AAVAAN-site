<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestSpecialtyVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isArtist() ?? false;
    }

    protected function prepareForValidation(): void
    {
        // حذف لینک‌های خالی پیش از اعتبارسنجی (فرم تا ۳ فیلد لینک دارد).
        $links = array_values(array_filter(
            (array) $this->input('evidence_links', []),
            fn ($v) => is_string($v) && trim($v) !== ''
        ));
        $this->merge(['evidence_links' => $links]);
    }

    public function rules(): array
    {
        return [
            'artist_note'       => ['required', 'string', 'max:1000'],
            'evidence_links'    => ['nullable', 'array', 'max:3'],
            'evidence_links.*'  => ['url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'artist_note.required' => 'توضیح کوتاه دربارهٔ تخصص الزامی است.',
            'artist_note.max'      => 'توضیح نباید بیشتر از ۱۰۰۰ کاراکتر باشد.',
            'evidence_links.max'   => 'حداکثر ۳ لینک مدرک مجاز است.',
            'evidence_links.*.url' => 'لینک مدرک باید یک آدرس معتبر باشد.',
        ];
    }
}
