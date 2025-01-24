<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlFeaturesTables extends Migration
{
    public function up()
    {
        Schema::create('skill_vectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->json('vector_data');
            $table->timestamps();
        });

        Schema::create('career_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('previous_role');
            $table->string('new_role');
            $table->json('skills_gained');
            $table->timestamps();
        });

        Schema::create('course_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->float('similarity_score');
            $table->boolean('is_viewed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_recommendations');
        Schema::dropIfExists('career_transitions');
        Schema::dropIfExists('skill_vectors');
    }
}
