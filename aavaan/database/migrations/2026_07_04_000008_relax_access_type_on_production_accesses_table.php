<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * افزودن یادداشت ادمین + آزادسازی ستون access_type از قید enum تا مقدار «manual»
     * برای اعتبارهای دستی مجاز شود.
     *
     * چون doctrine/dbal نصب نیست، از تغییرِ ستون به‌صورت driver-specific استفاده می‌کنیم:
     *  - MariaDB/MySQL: MODIFY ستون enum به VARCHAR.
     *  - SQLite (تست): بازسازی جدول برای حذف قید CHECK (بدون نیاز به dbal).
     */
    public function up(): void
    {
        Schema::table('production_accesses', function (Blueprint $table) {
            $table->text('admin_note')->nullable()->after('expires_at');
        });

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE production_accesses MODIFY access_type VARCHAR(20) NOT NULL DEFAULT 'single'");
            return;
        }

        // SQLite: بازسازی جدول بدون قید enum روی access_type.
        Schema::disableForeignKeyConstraints();
        DB::statement("
            CREATE TABLE production_accesses_tmp (
                id integer primary key autoincrement not null,
                user_id integer not null,
                access_type varchar not null default 'single',
                bundle_size integer not null default '1',
                used_count integer not null default '0',
                expires_at datetime,
                admin_note text,
                created_at datetime,
                updated_at datetime
            )
        ");
        DB::statement("
            INSERT INTO production_accesses_tmp
                (id, user_id, access_type, bundle_size, used_count, expires_at, admin_note, created_at, updated_at)
            SELECT id, user_id, access_type, bundle_size, used_count, expires_at, admin_note, created_at, updated_at
            FROM production_accesses
        ");
        DB::statement("DROP TABLE production_accesses");
        DB::statement("ALTER TABLE production_accesses_tmp RENAME TO production_accesses");
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE production_accesses MODIFY access_type ENUM('single','bundle_5','bundle_10') NOT NULL");
        }

        Schema::table('production_accesses', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });
    }
};
