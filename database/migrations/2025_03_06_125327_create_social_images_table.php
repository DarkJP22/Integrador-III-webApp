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
        Schema::create('social_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('public_id');
            $table->string('type')->default('image');
            $table->string('format')->nullable();
            $table->integer('duration')->default(0);
            $table->string('url');
            $table->timestamps();
        });
        Schema::create('social_stories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('public_id');
            $table->string('url');
            $table->string('type')->default('image');
            $table->string('format')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('views')->default(0);
            $table->timestamps();
        });

        \App\Setting::updateOrCreate([
            'option' => 'upload_story_image_max_filesize_in_mb',
        ], [
            'value' => 4,
        ]);
        \App\Setting::updateOrCreate([
            'option' => 'upload_story_video_max_filesize_in_mb',
        ], [
            'value' => 30,
        ]);
        \App\Setting::updateOrCreate([
            'option' => 'upload_story_video_max_length_in_minutes',
        ], [
            'value' => 2,
        ]);

        \App\Setting::updateOrCreate([
            'option' => 'limit_story_upload_videos',
        ], [
            'value' => 0,
        ]);
        \App\Setting::updateOrCreate([
            'option' => 'limit_story_upload_images',
        ], [
            'value' => 50,
        ]);
        \App\Setting::updateOrCreate([
            'option' => 'limit_story_by_day',
        ], [
            'value' => 10,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_images');
        Schema::dropIfExists('social_stories');
    }
};
