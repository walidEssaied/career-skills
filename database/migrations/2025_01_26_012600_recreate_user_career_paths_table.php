<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('user_career_paths');

        Schema::create('user_career_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('career_path_id')->constrained()->onDelete('cascade');
            $table->timestamp('target_completion_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'career_path_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_career_paths');
    }
};
