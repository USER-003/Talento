<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_one');
            $table->unsignedBigInteger('user_two');
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->unsignedBigInteger('blocked_by')->nullable();
            $table->timestamps();
            
            $table->foreign('user_one')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('user_two')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('blocked_by')->references('id_usuario')->on('usuarios')->onDelete('set null');
            
            // Ensure unique conversations between two users
            $table->unique(['user_one', 'user_two']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
