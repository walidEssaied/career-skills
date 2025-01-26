<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->integer('importance_level')->default(1);
            $table->integer('skill_level_gained')->default(1);
            $table->timestamps();
            $table->unique(['course_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_skill');
    }
};
