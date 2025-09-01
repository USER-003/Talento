<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id'); // que emite
            $table->unsignedBigInteger('client_id');   // a quién se le cobra
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('description')->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid, canceled
            $table->date('due_date')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->timestamps();

            $table->foreign('provider_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('client_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('servicio_id')->references('id_servicios_personales')->on('servicios_personales')->onDelete('set null');
            $table->index(['provider_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
