<?php

namespace App\Services;

use App\Models\ArtistSpecialty;
use App\Models\SpecialtyAttributeDefinition;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * سازندهٔ ایندکس نرمال‌شدهٔ ویژگی‌ها (جدول artist_specialty_attribute_values) از روی
 * ستون JSON «attributes» هر تخصص. منبع حقیقت همان JSON است؛ این سرویس فقط ایندکس فیلترپذیر می‌سازد.
 *
 * نکته: مقادیر با «کلید» تعریف (key) با ستون‌های تعریف تطبیق داده می‌شوند، نه با id قدیمی؛
 * چون با هر بار seed مجدد جدول تعریف‌ها truncate می‌شود و idها عوض می‌شوند، ولی کلیدها پایدارند.
 */
class SpecialtyAttributeIndexer
{
    /**
     * ردیف‌های ایندکس این تخصص را پاک و از روی JSON بازسازی می‌کند.
     */
    public function sync(ArtistSpecialty $specialty): void
    {
        DB::transaction(function () use ($specialty) {
            // پاک‌سازی ایندکس قبلی این تخصص.
            $specialty->attributeValues()->delete();

            $attributes = $specialty->attributes ?? [];
            if (empty($attributes)) {
                return;
            }

            // تعریف‌های مؤثر این دسته (زیرشاخه‌ها از والد ارث می‌برند)، نگاشت‌شده با key.
            $definitions = $specialty->category
                ? $specialty->category->effectiveAttributeDefinitions()->keyBy('key')
                : collect();

            if ($definitions->isEmpty()) {
                return;
            }

            $now  = Carbon::now();
            $rows = [];

            foreach ($attributes as $key => $value) {
                /** @var SpecialtyAttributeDefinition|null $def */
                $def = $definitions->get($key);
                if (!$def) {
                    continue; // کلید بی‌تعریف (مثلاً ویژگی حذف‌شده) نادیده گرفته می‌شود.
                }

                foreach ($this->rowsForValue($def, $value) as $row) {
                    $rows[] = array_merge($row, [
                        'artist_specialty_id' => $specialty->id,
                        'definition_id'       => $def->id,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ]);
                }
            }

            if (!empty($rows)) {
                $specialty->attributeValues()->getRelated()->newQuery()->insert($rows);
            }
        });
    }

    /**
     * از روی نوع فیلد و مقدار خام، ردیف‌های value_string/value_number را می‌سازد.
     * انواع غیرقابل فیلتر (textarea / url / file_link) ردیفی نمی‌سازند.
     *
     * @return array<int, array{value_string: ?string, value_number: ?string}>
     */
    private function rowsForValue(SpecialtyAttributeDefinition $def, mixed $value): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        switch ($def->field_type) {
            case 'multiselect':
                // به‌ازای هر گزینهٔ انتخاب‌شده یک ردیف جدا.
                $rows = [];
                foreach ((array) $value as $option) {
                    $option = (string) $option;
                    if ($option === '') {
                        continue;
                    }
                    $rows[] = ['value_string' => mb_substr($option, 0, 191), 'value_number' => null];
                }
                return $rows;

            case 'boolean':
                // مقدار '1' یا '0' در value_string.
                $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                return [['value_string' => $bool ? '1' : '0', 'value_number' => null]];

            case 'number':
                if (!is_numeric($value)) {
                    return [];
                }
                return [['value_string' => null, 'value_number' => (string) $value]];

            case 'date':
                // فقط تاریخ معتبر با فرمت Y-m-d ایندکس می‌شود.
                try {
                    $date = Carbon::parse((string) $value)->format('Y-m-d');
                } catch (\Throwable) {
                    return [];
                }
                return [['value_string' => $date, 'value_number' => null]];

            case 'select':
            case 'text':
                return [['value_string' => mb_substr((string) $value, 0, 191), 'value_number' => null]];

            // textarea / url / file_link و هر نوع دیگر: قابل فیلتر نیست، ردیفی ساخته نمی‌شود.
            default:
                return [];
        }
    }
}
