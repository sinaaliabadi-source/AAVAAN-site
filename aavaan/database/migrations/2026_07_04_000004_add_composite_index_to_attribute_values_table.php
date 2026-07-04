<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ایندکس ترکیبی برای کوئری فیلتر «کست‌یاب».
     *
     * فیلترهای ویژگی به‌صورت whereExists همبسته اجرا می‌شوند:
     *   v.artist_specialty_id = sp.id  AND  v.definition_id = ?  AND  v.value_number BETWEEN ...
     * ایندکس یکتای موجود (asav_unique) با value_string است و برای value_number کمکی نیست؛
     * asav_def_number_idx هم با definition_id شروع می‌شود، نه با artist_specialty_id.
     * این ایندکس ترکیبی، جست‌وجوی بازهٔ عددیِ همبسته با تخصص را کاملاً پوشش می‌دهد.
     */
    public function up(): void
    {
        Schema::table('artist_specialty_attribute_values', function (Blueprint $table) {
            $table->index(
                ['artist_specialty_id', 'definition_id', 'value_number'],
                'asav_sp_def_number_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('artist_specialty_attribute_values', function (Blueprint $table) {
            $table->dropIndex('asav_sp_def_number_idx');
        });
    }
};
