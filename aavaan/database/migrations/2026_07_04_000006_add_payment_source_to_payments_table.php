<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // تفکیک روش پرداخت: درگاه (gateway) یا ثبت دستی توسط ادمین (manual).
            // پیش‌فرض gateway تا رکوردهای موجود دست‌نخورده بمانند و گزارش‌ها نشکنند.
            $table->enum('payment_source', ['gateway', 'manual'])->default('gateway')->after('gateway');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_source');
        });
    }
};
