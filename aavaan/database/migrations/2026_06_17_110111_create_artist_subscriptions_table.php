<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('plan', ['monthly', 'yearly']);
            $table->unsignedInteger('amount');
            $table->string('payment_ref')->nullable();
            $table->string('payment_authority')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_subscriptions');
    }
};
