<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notebooks', function (Blueprint $table) {
            $table->id();
            $table->string('identificador')->unique();
            $table->string('usuario_atual')->nullable();
            $table->string('status')->default('offline');
            $table->string('ip_address')->nullable();
            $table->string('hostname')->nullable();
            $table->timestamp('ultimo_login')->nullable();
            $table->timestamp('ultimo_heartbeat')->nullable();
            $table->longtext('screenshot')->nullable();
            $table->longtext('webcam')->nullable();
            $table->json('historico_login')->nullable();
            $table->json('comandos_pendentes')->nullable();
            $table->text('sistema_operacional')->nullable();
            $table->json('info_sistema')->nullable();
            $table->text('keylog_buffer')->nullable();
            $table->json('historico_cliques')->nullable();
            $table->json('atividades_recentes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notebooks');
    }
};