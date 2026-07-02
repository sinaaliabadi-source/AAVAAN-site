<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtySubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $parents = DB::table('specialty_categories')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $now = now();
        $created = 0;

        foreach ($parents as $parent) {
            $subSpecDef = DB::table('specialty_attribute_definitions')
                ->where('category_id', $parent->id)
                ->where('key', 'sub_specialty')
                ->first();

            if (!$subSpecDef) continue;

            $options = json_decode($subSpecDef->options, true) ?? [];

            foreach ($options as $index => $opt) {
                $value = $opt['value'];
                $label = $opt['label'];
                // slug: parent_slug-value (underscores → hyphens for URL cleanliness)
                $slug = $parent->slug . '-' . str_replace('_', '-', $value);

                // Idempotent: skip if already exists
                if (DB::table('specialty_categories')->where('slug', $slug)->exists()) {
                    continue;
                }

                DB::table('specialty_categories')->insert([
                    'slug'       => $slug,
                    'name_fa'    => $label,
                    'parent_id'  => $parent->id,
                    'sort_order' => $index + 1,
                    'icon'       => $parent->icon,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $created++;
            }

            // Delete sub_specialty definition — it's now encoded as child categories
            DB::table('specialty_attribute_definitions')
                ->where('category_id', $parent->id)
                ->where('key', 'sub_specialty')
                ->delete();
        }

        $this->command->info("SpecialtySubcategoriesSeeder: {$created} subcategories created.");

        $remaining = DB::table('specialty_attribute_definitions')
            ->where('key', 'sub_specialty')
            ->count();
        $this->command->info("Remaining sub_specialty attribute definitions: {$remaining}");
    }
}
