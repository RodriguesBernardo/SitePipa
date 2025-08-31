<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('short_description');
            $table->text('long_description');
            $table->string('cover_image');
            $table->string('file_path');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('game_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('game_game_tag', function (Blueprint $table) {
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_tag_id')->constrained()->onDelete('cascade');
            $table->primary(['game_id', 'game_tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_game_tag');
        Schema::dropIfExists('game_tags');
        Schema::dropIfExists('games');
    }
};