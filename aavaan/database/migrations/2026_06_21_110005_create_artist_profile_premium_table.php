<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artist_profile_premium', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('stage_name')->nullable();
            $table->string('legal_name')->nullable();
            $table->boolean('willing_to_travel')->default(false);
            $table->boolean('willing_long_stay')->default(false);
            $table->string('military_status')->nullable();
            // values: not_required, completed, exempt, active
            $table->string('passport_status')->nullable();
            // values: valid, expired, none
            $table->boolean('international_collaboration')->default(false);
            $table->unsignedInteger('completed_projects_count')->default(0);
            $table->unsignedInteger('published_projects_count')->default(0);
            $table->json('awards')->nullable();
            $table->json('memberships')->nullable();
            $table->string('availability_status')->default('ready');
            // values: ready, busy, available_from
            $table->date('available_from_date')->nullable();
            $table->unsignedSmallInteger('concurrent_capacity')->nullable();
            $table->unsignedInteger('day_rate_min')->nullable();
            $table->unsignedInteger('day_rate_max')->nullable();
            $table->boolean('show_day_rate')->default(false);
            $table->string('imdb_url', 500)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->string('vimeo_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_profile_premium');
    }
};
