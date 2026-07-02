<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('specialty_attribute_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('specialty_categories')->cascadeOnDelete();
            $table->string('key');
            $table->string('label_fa');
            $table->string('field_type')->default('text');
            // enum values: text, textarea, number, boolean, select, multiselect, url, date, file_link
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->string('visibility')->default('public');
            // enum values: public, production_team_only
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialty_attribute_definitions');
    }
};
