<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('provider');
            $table->string('url');
            $table->string('duration');
            $table->string('difficulty_level');
            $table->decimal('price', 10, 2);
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('reviews_count')->default(0);
            $table->string('language');
            $table->boolean('certificate_offered');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('course_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->integer('skill_level_gained');
            $table->timestamps();

            $table->unique(['course_id', 'skill_id']);
        });

        Schema::create('career_path_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_path_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('order');
            $table->boolean('required');
            $table->timestamps();

            $table->unique(['career_path_id', 'course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('career_path_courses');
        Schema::dropIfExists('course_skills');
        Schema::dropIfExists('courses');
    }
};
