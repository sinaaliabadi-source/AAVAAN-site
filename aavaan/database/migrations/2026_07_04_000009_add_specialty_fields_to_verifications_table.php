<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * اتصال جدول تأییدها به تخصص‌های هنرمند برای جریان «تأیید تخصص».
     * مقدار type برای این جریان «specialty» است؛ مقادیر قبلی (phone/resume/professional) دست‌نخورده می‌مانند.
     */
    public function up(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->foreignId('artist_specialty_id')->nullable()->after('user_id')
                  ->constrained('artist_specialties')->cascadeOnDelete();
            $table->text('artist_note')->nullable()->after('type');          // توضیح هنرمند
            $table->json('evidence_links')->nullable()->after('artist_note'); // لینک مدارک/نمونه‌کار
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropConstrainedForeignId('artist_specialty_id');
            $table->dropColumn(['artist_note', 'evidence_links']);
        });
    }
};
