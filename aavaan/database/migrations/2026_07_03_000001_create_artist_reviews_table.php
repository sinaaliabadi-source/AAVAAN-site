<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('artist_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            // هر تیم تولید فقط یک نظر برای هر هنرمند می‌تواند ثبت کند
            $table->unique(['reviewer_user_id', 'artist_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_reviews');
    }
};
