<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_access_id')->constrained('production_accesses')->cascadeOnDelete();
            $table->foreignId('production_user_id')->constrained('users');
            $table->foreignId('artist_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamp('accessed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['production_user_id', 'artist_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_access_logs');
    }
};
