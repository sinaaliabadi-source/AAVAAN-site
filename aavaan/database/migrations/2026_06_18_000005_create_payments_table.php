<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable'); // payable_type + payable_id (Subscription or ProductionAccess)
            $table->unsignedInteger('amount');
            $table->string('gateway')->default('zarinpal');
            $table->string('authority')->nullable()->comment('gateway authority code before payment');
            $table->string('ref_id')->nullable()->comment('gateway reference ID after success');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
