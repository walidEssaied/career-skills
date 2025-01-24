<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->integer('proficiency_level');
            $table->integer('target_level');
            $table->timestamp('last_practiced_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('verification_method')->nullable();
            $table->integer('endorsements_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_skills');
    }
};
