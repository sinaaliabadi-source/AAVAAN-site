<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('username')->unique()->nullable();
            $table->string('field');
            $table->string('city')->nullable();
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('reel_video')->nullable();
            $table->boolean('reel_is_external')->default(false);
            $table->string('phone_contact')->nullable();
            $table->string('email_contact')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('profile_views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_profiles');
    }
};
