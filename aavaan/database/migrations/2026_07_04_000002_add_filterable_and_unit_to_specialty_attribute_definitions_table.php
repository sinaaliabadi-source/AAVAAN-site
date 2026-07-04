<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specialty_attribute_definitions', function (Blueprint $table) {
            // مشخص می‌کند این ویژگی در جستجوی تیم تولید (فاز بعد) به‌عنوان فیلتر نمایش داده شود.
            $table->boolean('is_filterable')->default(false)->after('visibility');
            // واحد فیلدهای عددی، مثل «سانتی‌متر» یا «کیلوگرم».
            $table->string('unit', 20)->nullable()->after('field_type');
        });
    }

    public function down(): void
    {
        Schema::table('specialty_attribute_definitions', function (Blueprint $table) {
            $table->dropColumn(['is_filterable', 'unit']);
        });
    }
};
