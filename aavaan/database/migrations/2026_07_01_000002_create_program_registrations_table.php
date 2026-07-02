<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name', 100);
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('city');
            $table->string('province');
            $table->unsignedSmallInteger('birth_year');
            $table->enum('gender', ['male', 'female']);
            $table->string('talent_type');
            $table->text('talent_description');
            $table->string('video_url')->nullable();
            $table->string('guardian_name', 100);
            $table->string('guardian_phone', 20);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'status']);
            $table->index('province');
            $table->index('talent_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_registrations');
    }
};
