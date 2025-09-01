<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('servicio_comentarios')) {
            Schema::create('servicio_comentarios', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('servicio_id');
                $table->foreign('servicio_id')
                    ->references('id_servicios_personales')
                    ->on('servicios_personales')
                    ->onDelete('cascade');
                $table->unsignedBigInteger('id_usuario');
                $table->foreign('id_usuario')
                    ->references('id_usuario')
                    ->on('usuarios')
                    ->onDelete('cascade');
                $table->unsignedTinyInteger('rating');
                $table->text('comentario');
                $table->timestamps();
                $table->unique(['servicio_id', 'id_usuario']);
            });
        }
    }

    public function down(): void
    {
        // Do not drop conditionally created table to avoid removing existing data unintentionally
        // If you need to drop it, uncomment the next line
        // Schema::dropIfExists('servicio_comentarios');
    }
};
