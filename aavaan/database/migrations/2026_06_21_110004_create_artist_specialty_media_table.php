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
        Schema::create('artist_specialty_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_specialty_id')->constrained('artist_specialties')->cascadeOnDelete();
            $table->string('type')->default('photo');
            // enum values: photo, video_link, document
            $table->string('file_path')->nullable();
            $table->string('external_url', 500)->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_specialty_media');
    }
};
