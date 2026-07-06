<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model {
    protected $fillable = ['code','type','value','max_uses','used_count','valid_from','valid_until','is_active','created_by'];
    protected $casts = [
        'is_active'   => 'boolean',
        'value'       => 'decimal:2',
        'valid_from'  => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function uses(): HasMany { return $this->hasMany(DiscountCodeUse::class); }

    public function isValid(): bool {
        return $this->validationError() === null;
    }

    /**
     * دلیل نامعتبر بودن کد به فارسی، یا null اگر معتبر است.
     * پیام‌ها برای نمایش مستقیم به کاربر مناسب‌اند (نامعتبر/منقضی/سقف‌پرشده/هنوز فعال‌نشده).
     */
    public function validationError(): ?string {
        if (!$this->is_active) {
            return 'این کد تخفیف معتبر نیست.';
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return 'این کد تخفیف هنوز فعال نشده است.';
        }
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return 'این کد تخفیف منقضی شده است.';
        }
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'ظرفیت استفاده از این کد تخفیف تکمیل شده است.';
        }
        return null;
    }

    /**
     * مبلغ پس از اعمال تخفیف روی یک مبلغ پایه (تومان). هرگز منفی نمی‌شود.
     */
    public function discountedAmount(int $amount): int {
        $final = $this->type === 'percent'
            ? $amount * (1 - ((float) $this->value / 100))
            : $amount - (float) $this->value;

        return (int) max(0, round($final));
    }

    /**
     * ثبت اتمیک مصرف کد: افزایش used_count فقط اگر هنوز به سقف نرسیده باشد.
     * یک UPDATE شرطیِ سازگار با MariaDB (بدون race روی سقف استفاده).
     *
     * @return bool  true اگر مصرف ثبت شد؛ false اگر کد در این لحظه دیگر مصرف‌شدنی نبود.
     */
    public static function consumeAtomically(int $id): bool {
        $affected = static::where('id', $id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->increment('used_count');

        return $affected > 0;
    }
}
