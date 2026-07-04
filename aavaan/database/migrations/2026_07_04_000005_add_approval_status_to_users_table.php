<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // گردش‌کار تأیید تیم‌های تولید.
            // نکتهٔ حیاتی سازگاری با گذشته: پیش‌فرض «approved» است تا کاربران فعلی سرور
            // (هنرمندان و تیم‌های موجود) قفل نشوند. فقط ثبت‌نام جدید production مقدار pending می‌گیرد.
            $table->string('approval_status', 20)->default('approved')->after('is_banned');
            // مقادیر: pending, approved, rejected
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                  ->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->index(['role', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'approval_status']);
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['approval_status', 'approved_at', 'rejection_reason']);
        });
    }
};
