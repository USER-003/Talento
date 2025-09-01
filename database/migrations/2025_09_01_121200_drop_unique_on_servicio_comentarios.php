<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop unique constraint if exists
        Schema::table('servicio_comentarios', function (Blueprint $table) {
            try {
                $table->dropUnique(['servicio_id', 'id_usuario']);
            } catch (Throwable $e) {
                // Fallback for named indexes (common in pgsql)
                try {
                    $table->dropUnique('servicio_comentarios_servicio_id_id_usuario_unique');
                } catch (Throwable $e2) {
                    // ignore if already dropped
                }
            }
        });
    }

    public function down(): void
    {
        // Recreate unique if needed
        Schema::table('servicio_comentarios', function (Blueprint $table) {
            try {
                $table->unique(['servicio_id', 'id_usuario']);
            } catch (Throwable $e) {
                // ignore
            }
        });
    }
};
