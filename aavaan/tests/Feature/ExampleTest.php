<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    // صفحهٔ اصلی به جدول‌های هنرمندان برگزیده و دسته‌بندی‌ها کوئری می‌زند؛
    // بدون مهاجرت پایگاه‌داده، این کوئری‌ها خطا می‌دهند.
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
