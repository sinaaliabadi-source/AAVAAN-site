<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Festival;
use Illuminate\Console\Command;

/**
 * اعطای «اشتراک جشنوارهٔ افتتاح» به همهٔ هنرمندانی که اشتراک فعال ندارند.
 * idempotent: اجرای دوباره اشتراک تکراری نمی‌سازد.
 */
class FestivalGrant extends Command
{
    protected $signature = 'festival:grant';

    protected $description = 'ساخت اشتراک جشنواره برای همهٔ هنرمندان بدون اشتراک فعال (idempotent).';

    public function handle(): int
    {
        if (! Festival::active()) {
            $this->warn('جشنواره فعال نیست؛ هیچ اشتراکی ساخته نشد.');
            return self::SUCCESS;
        }

        $created = 0;

        User::where('role', 'artist')
            ->chunkById(200, function ($artists) use (&$created) {
                foreach ($artists as $artist) {
                    if (Festival::grantSubscription($artist) !== null) {
                        $created++;
                    }
                }
            });

        $this->info("اشتراک جشنواره برای {$created} هنرمند ساخته شد.");

        return self::SUCCESS;
    }
}
