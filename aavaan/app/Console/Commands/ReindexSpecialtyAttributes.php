<?php

namespace App\Console\Commands;

use App\Models\ArtistSpecialty;
use App\Services\SpecialtyAttributeIndexer;
use Illuminate\Console\Command;

class ReindexSpecialtyAttributes extends Command
{
    protected $signature   = 'specialties:reindex';
    protected $description = 'بازسازی ایندکس نرمال‌شدهٔ ویژگی‌ها (artist_specialty_attribute_values) برای همهٔ تخصص‌های موجود.';

    public function handle(SpecialtyAttributeIndexer $indexer): int
    {
        $total   = ArtistSpecialty::count();
        $done    = 0;
        $skipped = 0;

        if ($total === 0) {
            $this->info('هیچ تخصصی برای ایندکس وجود ندارد.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        ArtistSpecialty::with('category.parent.attributeDefinitions', 'category.attributeDefinitions')
            ->chunkById(200, function ($specialties) use ($indexer, &$done, &$skipped, $bar) {
                foreach ($specialties as $specialty) {
                    try {
                        $indexer->sync($specialty);
                        $done++;
                    } catch (\Throwable $e) {
                        $skipped++;
                        $this->newLine();
                        $this->warn("خطا در ایندکس تخصص #{$specialty->id}: {$e->getMessage()}");
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("ایندکس کامل شد. {$done} تخصص ایندکس شد" . ($skipped ? "، {$skipped} مورد با خطا رد شد." : '.'));

        return self::SUCCESS;
    }
}
