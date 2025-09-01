<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicio_comentarios', function (Blueprint $table) {
            $table->id();
            // Servicio reference
            $table->unsignedBigInteger('servicio_id');
            $table->foreign('servicio_id')
                ->references('id_servicios_personales')
                ->on('servicios_personales')
                ->onDelete('cascade');
            // Usuario reference
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');
            // Rating 1..5
            $table->unsignedTinyInteger('rating');
            // Comment text
            $table->text('comentario');
            $table->timestamps();
            // A user can only comment once per service by default (prevent spam), allow updates
            $table->unique(['servicio_id', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_comentarios');
    }
};
