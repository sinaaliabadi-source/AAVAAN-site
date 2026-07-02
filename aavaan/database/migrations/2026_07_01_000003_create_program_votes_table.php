<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('program_registrations')->cascadeOnDelete();
            $table->string('voter_ip', 45);
            $table->string('voter_phone', 20)->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->timestamps();

            // یک رأی از هر IP در هر برنامه
            $table->unique(['program_id', 'voter_ip']);
            $table->index('registration_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_votes');
    }
};
