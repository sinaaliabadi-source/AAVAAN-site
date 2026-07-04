<?php

namespace Tests\Feature;

use App\Models\ArtistSpecialty;
use App\Models\ArtistSpecialtyAttributeValue;
use App\Models\SpecialtyAttributeDefinition;
use App\Models\SpecialtyCategory;
use App\Models\User;
use App\Services\SpecialtyAttributeIndexer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ایندکس نرمال‌شدهٔ ویژگی‌ها (artist_specialty_attribute_values).
 * صحت قواعد sync، cascade حذف و backfill کامند reindex را می‌سنجد.
 */
class SpecialtyAttributeIndexTest extends TestCase
{
    use RefreshDatabase;

    private function makeCategoryWithDefs(): SpecialtyCategory
    {
        $cat = SpecialtyCategory::create([
            'slug' => 'test-cat', 'name_fa' => 'دستهٔ آزمایشی',
            'parent_id' => null, 'sort_order' => 1, 'is_active' => true,
        ]);

        $defs = [
            ['key' => 'height_cm',  'field_type' => 'number',      'unit' => 'سانتی‌متر'],
            ['key' => 'skills',     'field_type' => 'multiselect',
             'options' => [['value' => 'swim', 'label' => 'شنا'], ['value' => 'sing', 'label' => 'آواز'], ['value' => 'ride', 'label' => 'سوارکاری']]],
            ['key' => 'has_gear',   'field_type' => 'boolean'],
            ['key' => 'sub',        'field_type' => 'select',
             'options' => [['value' => 'a', 'label' => 'الف'], ['value' => 'b', 'label' => 'ب']]],
            ['key' => 'notes',      'field_type' => 'textarea'],
            ['key' => 'reel',       'field_type' => 'url'],
            ['key' => 'available',  'field_type' => 'date'],
            ['key' => 'nickname',   'field_type' => 'text'],
        ];

        $order = 0;
        foreach ($defs as $d) {
            SpecialtyAttributeDefinition::create(array_merge([
                'category_id' => $cat->id,
                'label_fa'    => 'برچسب ' . $d['key'],
                'is_required' => false,
                'visibility'  => 'public',
                'sort_order'  => ++$order,
            ], $d));
        }

        return $cat->fresh('attributeDefinitions');
    }

    private function makeArtist(): User
    {
        return User::factory()->create(['role' => 'artist']);
    }

    public function test_sync_builds_normalized_rows_per_field_type(): void
    {
        $cat  = $this->makeCategoryWithDefs();
        $user = $this->makeArtist();

        $specialty = ArtistSpecialty::create([
            'user_id'     => $user->id,
            'category_id' => $cat->id,
            'attributes'  => [
                'height_cm' => 182,
                'skills'    => ['swim', 'sing'],
                'has_gear'  => true,
                'sub'       => 'b',
                'notes'     => 'یک متن طولانی که نباید ایندکس شود',
                'reel'      => 'https://example.com/x',
                'available' => '2026-03-21',
                'nickname'  => 'شبح',
            ],
        ]);

        app(SpecialtyAttributeIndexer::class)->sync($specialty);

        $rows = ArtistSpecialtyAttributeValue::where('artist_specialty_id', $specialty->id)->get();

        // number → یک ردیف با value_number
        $height = $rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'height_cm')->id);
        $this->assertNotNull($height);
        $this->assertEquals('182.00', $height->value_number);
        $this->assertNull($height->value_string);

        // multiselect → دو ردیف جدا
        $skillDefId = $cat->attributeDefinitions->firstWhere('key', 'skills')->id;
        $skillRows  = $rows->where('definition_id', $skillDefId)->pluck('value_string')->sort()->values()->all();
        $this->assertEquals(['sing', 'swim'], $skillRows);

        // boolean → value_string '1'
        $boolDefId = $cat->attributeDefinitions->firstWhere('key', 'has_gear')->id;
        $this->assertEquals('1', $rows->firstWhere('definition_id', $boolDefId)->value_string);

        // select / text / date ایندکس می‌شوند
        $this->assertEquals('b', $rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'sub')->id)->value_string);
        $this->assertEquals('شبح', $rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'nickname')->id)->value_string);
        $this->assertEquals('2026-03-21', $rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'available')->id)->value_string);

        // textarea و url ایندکس نمی‌شوند
        $this->assertNull($rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'notes')->id));
        $this->assertNull($rows->firstWhere('definition_id', $cat->attributeDefinitions->firstWhere('key', 'reel')->id));
    }

    public function test_boolean_false_indexes_zero(): void
    {
        $cat  = $this->makeCategoryWithDefs();
        $user = $this->makeArtist();

        $specialty = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'attributes' => ['has_gear' => false],
        ]);
        app(SpecialtyAttributeIndexer::class)->sync($specialty);

        $boolDefId = $cat->attributeDefinitions->firstWhere('key', 'has_gear')->id;
        $this->assertEquals('0', ArtistSpecialtyAttributeValue::where('definition_id', $boolDefId)->value('value_string'));
    }

    public function test_resync_replaces_previous_rows(): void
    {
        $cat  = $this->makeCategoryWithDefs();
        $user = $this->makeArtist();
        $indexer = app(SpecialtyAttributeIndexer::class);

        $specialty = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'attributes' => ['skills' => ['swim', 'sing', 'ride']],
        ]);
        $indexer->sync($specialty);
        $this->assertEquals(3, $specialty->attributeValues()->count());

        $specialty->update(['attributes' => ['skills' => ['swim']]]);
        $indexer->sync($specialty->fresh());
        $this->assertEquals(1, $specialty->attributeValues()->count());
    }

    public function test_delete_cascades_index_rows(): void
    {
        $cat  = $this->makeCategoryWithDefs();
        $user = $this->makeArtist();

        $specialty = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'attributes' => ['height_cm' => 170, 'skills' => ['swim']],
        ]);
        app(SpecialtyAttributeIndexer::class)->sync($specialty);
        $id = $specialty->id;
        $this->assertGreaterThan(0, ArtistSpecialtyAttributeValue::where('artist_specialty_id', $id)->count());

        $specialty->delete();
        $this->assertEquals(0, ArtistSpecialtyAttributeValue::where('artist_specialty_id', $id)->count());
    }

    public function test_reindex_command_backfills(): void
    {
        $cat  = $this->makeCategoryWithDefs();
        $user = $this->makeArtist();

        // تخصص بدون ایندکس (شبیه‌سازی دادهٔ قدیمی).
        $specialty = ArtistSpecialty::create([
            'user_id' => $user->id, 'category_id' => $cat->id,
            'attributes' => ['height_cm' => 165, 'skills' => ['swim', 'sing']],
        ]);
        $this->assertEquals(0, $specialty->attributeValues()->count());

        $this->artisan('specialties:reindex')->assertSuccessful();

        $this->assertEquals(3, $specialty->attributeValues()->count());
    }
}
