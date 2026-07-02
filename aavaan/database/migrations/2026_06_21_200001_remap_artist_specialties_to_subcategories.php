<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data migration: move artist_specialties rows that point to a parent category
 * (parent_id IS NULL) and carry a sub_specialty attribute to the matching
 * leaf category, removing the sub_specialty key from attributes.
 *
 * Safe to run on an empty artist_specialties table — exits without error.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Load parent categories keyed by id
        $parents = DB::table('specialty_categories')
            ->whereNull('parent_id')
            ->get()
            ->keyBy('id');

        if ($parents->isEmpty()) return;

        // Load all child categories and build lookup: [parent_id][original_value] → child_id
        // Child slug pattern: {parent_slug}-{value_with_hyphens}
        $children = DB::table('specialty_categories')
            ->whereNotNull('parent_id')
            ->get();

        $childMap = [];
        foreach ($children as $child) {
            $parent = $parents[$child->parent_id] ?? null;
            if (!$parent) continue;

            $prefix = $parent->slug . '-';
            if (!str_starts_with($child->slug, $prefix)) continue;

            $suffix = substr($child->slug, strlen($prefix));
            // Convert hyphens back to underscores to match the original value
            $originalValue = str_replace('-', '_', $suffix);
            $childMap[$child->parent_id][$originalValue] = $child->id;
        }

        if (empty($childMap)) return;

        // Process artist_specialties rows referencing parent categories
        $parentIds = $parents->keys()->all();
        $specialties = DB::table('artist_specialties')
            ->whereIn('category_id', $parentIds)
            ->get();

        foreach ($specialties as $spec) {
            $attrs = json_decode($spec->attributes, true) ?? [];

            if (!array_key_exists('sub_specialty', $attrs)) continue;

            $subValue = $attrs['sub_specialty'];
            $catId    = $spec->category_id;
            $childId  = $childMap[$catId][$subValue] ?? null;

            if (!$childId) continue;

            unset($attrs['sub_specialty']);

            DB::table('artist_specialties')
                ->where('id', $spec->id)
                ->update([
                    'category_id' => $childId,
                    'attributes'  => json_encode($attrs),
                    'updated_at'  => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Not reversible without restoring sub_specialty attribute definitions.
    }
};
