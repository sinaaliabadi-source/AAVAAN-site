<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * نسخهٔ نرمال‌شده و ایندکس‌پذیر مقادیر JSON برای فیلتر SQL.
     * منبع حقیقت همچنان ستون JSON «attributes» روی artist_specialties است؛
     * این جدول فقط ایندکس جستجو است و توسط SpecialtyAttributeIndexer بازسازی می‌شود.
     */
    public function up(): void
    {
        Schema::create('artist_specialty_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_specialty_id')->constrained('artist_specialties')->cascadeOnDelete();
            $table->foreignId('definition_id')->constrained('specialty_attribute_definitions')->cascadeOnDelete();
            $table->string('value_string', 191)->nullable();   // برای select / boolean / text کوتاه / date
            $table->decimal('value_number', 10, 2)->nullable(); // برای number
            $table->timestamps();

            // به‌ازای هر گزینهٔ multiselect یک ردیف جدا؛ به همین دلیل unique سه‌ستونه است.
            $table->unique(['artist_specialty_id', 'definition_id', 'value_string'], 'asav_unique');
            $table->index(['definition_id', 'value_string'], 'asav_def_string_idx');
            $table->index(['definition_id', 'value_number'], 'asav_def_number_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_specialty_attribute_values');
    }
};
