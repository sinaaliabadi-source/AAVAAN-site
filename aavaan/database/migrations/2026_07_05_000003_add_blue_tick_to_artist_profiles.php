<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * «تیک آبی آوان» — نشان برگزیدگی/اعتماد کلِ پروفایل که فقط ادمین اعطا می‌کند.
     * این نشان جدا از «تأیید تخصص» (verifications) است و نباید با آن قاطی شود.
     */
    public function up(): void
    {
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->boolean('has_blue_tick')->default(false)->after('is_active');
            $table->timestamp('blue_tick_granted_at')->nullable()->after('has_blue_tick');
            $table->index('has_blue_tick', 'artist_profiles_has_blue_tick_idx');
        });
    }

    public function down(): void
    {
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->dropIndex('artist_profiles_has_blue_tick_idx');
            $table->dropColumn(['has_blue_tick', 'blue_tick_granted_at']);
        });
    }
};
