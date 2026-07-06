<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * سازگاری با گذشته: پیش از فعال‌شدن «تأیید ایمیل»، هیچ حسابی email_verified_at نداشت.
     * برای اینکه هیچ کاربر فعلی سرور قفل نشود، تاریخ تأیید همهٔ کاربرانِ موجود (همهٔ نقش‌ها)
     * روی زمان اجرای مهاجرت ست می‌شود. این فقط ردیف‌های موجود را پر می‌کند؛ کاربران جدیدِ
     * پس از این تاریخ باید ایمیل خود را تأیید کنند (اجبار فقط روی نقش artist اعمال می‌شود).
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // برگشت‌پذیر نیست: نمی‌توان تشخیص داد کدام تأییدها از این backfill بوده‌اند.
    }
};
