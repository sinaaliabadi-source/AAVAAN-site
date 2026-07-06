<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * آزادسازی ستون plan اشتراک‌ها تا مقدار «festival» (اشتراک جشنوارهٔ افتتاح) مجاز شود.
     *
     * چون doctrine/dbal نصب نیست، تغییر ستون به‌صورت driver-specific انجام می‌شود:
     *  - MariaDB/MySQL: MODIFY ستون enum به VARCHAR (سازگار با مقادیر موجود).
     *  - SQLite (تست): بازسازی جدول برای حذف قید CHECK enum (بدون نیاز به dbal).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE subscriptions MODIFY plan VARCHAR(20) NOT NULL");
            return;
        }

        // SQLite: بازسازی جدول بدون قید enum روی plan.
        Schema::disableForeignKeyConstraints();
        DB::statement("
            CREATE TABLE subscriptions_tmp (
                id integer primary key autoincrement not null,
                user_id integer not null,
                plan varchar not null,
                status varchar not null default 'pending',
                starts_at datetime,
                expires_at datetime,
                admin_note text,
                created_at datetime,
                updated_at datetime
            )
        ");
        DB::statement("
            INSERT INTO subscriptions_tmp
                (id, user_id, plan, status, starts_at, expires_at, admin_note, created_at, updated_at)
            SELECT id, user_id, plan, status, starts_at, expires_at, admin_note, created_at, updated_at
            FROM subscriptions
        ");
        DB::statement("DROP TABLE subscriptions");
        DB::statement("ALTER TABLE subscriptions_tmp RENAME TO subscriptions");
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE subscriptions MODIFY plan ENUM('monthly','yearly') NOT NULL");
        }
        // در SQLite بازگردانی قید enum ضروری نیست (تست‌ها روی up اجرا می‌شوند).
    }
};
