<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('career_paths', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('industry');
            $table->integer('required_experience');
            $table->decimal('salary_range_min', 10, 2);
            $table->decimal('salary_range_max', 10, 2);
            $table->string('growth_potential');
            $table->string('market_demand');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('career_path_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_path_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->integer('importance_level');
            $table->timestamps();

            $table->unique(['career_path_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('career_path_skills');
        Schema::dropIfExists('career_paths');
    }
};
