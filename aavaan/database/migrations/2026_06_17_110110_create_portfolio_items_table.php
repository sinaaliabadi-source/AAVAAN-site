<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video_link'])->default('image');
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
