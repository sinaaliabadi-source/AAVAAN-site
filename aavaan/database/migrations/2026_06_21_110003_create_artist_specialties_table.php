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
        Schema::create('artist_specialties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('specialty_categories')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_specialties');
    }
};
