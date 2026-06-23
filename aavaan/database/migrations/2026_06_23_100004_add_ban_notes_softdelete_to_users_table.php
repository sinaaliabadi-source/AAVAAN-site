<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('admin_role');
            $table->text('admin_notes')->nullable()->after('is_banned');
            $table->softDeletes()->after('admin_notes');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'admin_notes', 'deleted_at']);
        });
    }
};
