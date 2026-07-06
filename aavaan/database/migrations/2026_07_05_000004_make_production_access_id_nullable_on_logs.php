<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * در «جشنوارهٔ افتتاح» تیم تولید تأییدشده بدون بستهٔ اعتبار پروفایل را باز می‌کند؛
     * پس رکورد لاگ دسترسی می‌تواند بدون production_access_id ثبت شود.
     * این ستون nullable می‌شود تا لاگِ جشنواره (بدون بستهٔ اعتبار) قابل ثبت باشد.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // FK باقی می‌ماند؛ NULL توسط قید خارجی بررسی نمی‌شود.
            DB::statement("ALTER TABLE production_access_logs MODIFY production_access_id BIGINT UNSIGNED NULL");
            return;
        }

        // SQLite: بازسازی جدول با ستونِ nullable و بدون FK قدیمی روی production_access_id.
        Schema::disableForeignKeyConstraints();
        DB::statement("
            CREATE TABLE production_access_logs_tmp (
                id integer primary key autoincrement not null,
                production_access_id integer,
                production_user_id integer not null,
                artist_profile_id integer not null,
                accessed_at datetime not null default CURRENT_TIMESTAMP,
                created_at datetime,
                updated_at datetime
            )
        ");
        DB::statement("
            INSERT INTO production_access_logs_tmp
                (id, production_access_id, production_user_id, artist_profile_id, accessed_at, created_at, updated_at)
            SELECT id, production_access_id, production_user_id, artist_profile_id, accessed_at, created_at, updated_at
            FROM production_access_logs
        ");
        DB::statement("DROP TABLE production_access_logs");
        DB::statement("ALTER TABLE production_access_logs_tmp RENAME TO production_access_logs");
        DB::statement("CREATE UNIQUE INDEX prod_access_logs_user_artist_unique ON production_access_logs (production_user_id, artist_profile_id)");
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE production_access_logs MODIFY production_access_id BIGINT UNSIGNED NOT NULL");
        }
    }
};
