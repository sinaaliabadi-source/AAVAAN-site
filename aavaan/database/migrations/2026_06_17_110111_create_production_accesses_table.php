<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('access_type', ['single', 'bundle_5', 'bundle_10']);
            $table->unsignedTinyInteger('bundle_size')->default(1);
            $table->unsignedTinyInteger('used_count')->default(0);
            $table->unsignedInteger('amount');
            $table->string('payment_ref')->nullable();
            $table->string('payment_authority')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_accesses');
    }
};
