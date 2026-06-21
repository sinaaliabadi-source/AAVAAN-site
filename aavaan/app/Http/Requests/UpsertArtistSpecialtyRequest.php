<?php

namespace App\Http\Requests;

use App\Models\SpecialtyAttributeDefinition;
use App\Models\SpecialtyCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertArtistSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isArtist() ?? false;
    }

    public function rules(): array
    {
        $base = [
            'category_id'      => ['required', 'integer', 'exists:specialty_categories,id'],
            'is_primary'       => ['boolean'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'attributes'       => ['nullable', 'array'],
        ];

        $categoryId = (int) $this->input('category_id');
        if ($categoryId > 0) {
            $base = array_merge($base, $this->buildAttributeRules($categoryId));
        }

        return $base;
    }

    /**
     * Build validation rules for the dynamic attribute fields of a given category.
     */
    private function buildAttributeRules(int $categoryId): array
    {
        $definitions = SpecialtyAttributeDefinition::where('category_id', $categoryId)->get();
        $rules       = [];

        foreach ($definitions as $def) {
            $fieldKey = "attributes.{$def->key}";

            if ($def->field_type === 'multiselect') {
                $rules[$fieldKey] = $def->is_required ? ['required', 'array'] : ['nullable', 'array'];
                $options = collect($def->options ?? [])->pluck('value')->filter()->values()->all();
                if (!empty($options)) {
                    $rules[$fieldKey . '.*'] = [Rule::in($options)];
                }
            } else {
                $fieldRules = $def->is_required ? ['required'] : ['nullable'];
                $fieldRules = array_merge($fieldRules, $this->rulesForFieldType($def));
                $rules[$fieldKey] = $fieldRules;
            }
        }

        return $rules;
    }

    private function rulesForFieldType(SpecialtyAttributeDefinition $def): array
    {
        return match ($def->field_type) {
            'text'        => ['string', 'max:500'],
            'textarea'    => ['string', 'max:3000'],
            'number'      => ['numeric'],
            'boolean'     => ['boolean'],
            'url'         => ['string', 'url', 'max:500'],
            'date'        => ['date'],
            'file_link'   => [
                'string',
                'max:500',
                'regex:/^https:\/\/(www\.)?aparat\.com\/v\/[A-Za-z0-9]+/',
            ],
            'select' => $this->selectRule($def),
            default       => ['string', 'max:500'],
        };
    }

    private function selectRule(SpecialtyAttributeDefinition $def): array
    {
        $options = collect($def->options ?? [])->pluck('value')->filter()->values()->all();

        return empty($options) ? ['string'] : [Rule::in($options)];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'انتخاب دسته‌بندی تخصصی الزامی است.',
            'category_id.exists'   => 'دسته‌بندی انتخاب‌شده معتبر نیست.',
            'years_experience.max' => 'سابقه کار نمی‌تواند بیشتر از ۶۰ سال باشد.',
        ];
    }
}
