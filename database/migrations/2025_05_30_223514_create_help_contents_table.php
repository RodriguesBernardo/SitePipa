<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('help_contents', function (Blueprint $table) {
            $table->id();
            $table->text('coordinators_content');
            $table->text('interns_content');
            $table->text('machines_usage_content');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('help_contents');
    }
};