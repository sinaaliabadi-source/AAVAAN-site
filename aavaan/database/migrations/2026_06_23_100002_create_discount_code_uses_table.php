<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('discount_code_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained('discount_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->foreign('subscription_id', 'dcu_sub_fk')->references('id')->on('subscriptions')->nullOnDelete();
            $table->timestamp('used_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('discount_code_uses'); }
};
