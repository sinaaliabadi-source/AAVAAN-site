<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artist_profiles', function (Blueprint $table) {
            // جنسیت یک ویژگی سراسری کستینگ است و به همهٔ هنرها مربوط می‌شود؛
            // برخلاف ویژگی‌های دسته‌ای، در پروفایل پایه نگهداری می‌شود.
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_year');
        });
    }

    public function down(): void
    {
        Schema::table('artist_profiles', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
