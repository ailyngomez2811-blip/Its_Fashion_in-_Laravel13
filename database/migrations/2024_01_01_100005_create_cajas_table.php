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
        Schema::create('cajas', function (Blueprint $table) {

            $table->id();

            // MONTOS
            $table->decimal('total_ingresos', 10, 2)->nullable();
            $table->decimal('total_egresos', 10, 2)->nullable();
            $table->decimal('saldo_inicial', 10, 2);
            $table->decimal('saldo_final', 10, 2)->nullable();
            $table->decimal('diferencia', 10, 2)->nullable();

            // FECHAS DE APERTURA Y CIERRE (dato de negocio)
            $table->dateTime('fecha_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();

            // JUSTIFICACIÓN Y ESTADO
            $table->text('justificacion')->nullable();
            $table->enum('estado', ['Abierta', 'Cerrada'])->nullable();

            // RELACIÓN CON USUARIO (QUIÉN ABRIÓ LA CAJA)
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // FECHAS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
