<?php

use App\Models\ArtistProfile;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * سازگاری با گذشته: پیش از این، ثبت‌نام هنرمند فیلد username را پر نمی‌کرد و پروفایل‌ها با
     * username=NULL ساخته می‌شدند. این پروفایل‌ها لینک عمومی نداشتند و باعث خطای ۵۰۰ می‌شدند.
     * این مهاجرت برای همهٔ پروفایل‌های بدون username، از روی نام کاربر مرتبط یک username یکتا
     * تولید می‌کند. از همان متد مرجع مدل (generateUniqueUsername) استفاده می‌شود تا منطق کپی نشود
     * و یکتایی تضمین گردد. کاملاً DB-agnostic است (بدون سینتکس خاص SQLite/MariaDB).
     */
    public function up(): void
    {
        ArtistProfile::whereNull('username')
            ->with('user')
            ->orderBy('id')
            ->each(function (ArtistProfile $profile) {
                $name = $profile->user->name ?? 'honarmand';
                $profile->username = ArtistProfile::generateUniqueUsername($name);
                $profile->save();
            });
    }

    public function down(): void
    {
        // برگشت‌پذیر نیست: نمی‌توان تشخیص داد کدام usernameها از این backfill بوده‌اند.
    }
};
