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
        Schema::create('ventas', function (Blueprint $table) {

            $table->id();

            // DATOS DE LA VENTA
            $table->dateTime('fecha')->nullable();
            $table->decimal('total', 10, 2);

            // RELACIÓN CON CLIENTE (usuario con rol Cliente)
            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // MÉTODO DE PAGO Y ESTADO
            $table->enum('metodo_pago', ['Efectivo', 'Transferencia bancaria'])->nullable();
            $table->enum('estado', ['Completada', 'Cancelada'])->nullable();

            // RELACIÓN CON USUARIO (QUIÉN REGISTRÓ LA VENTA)
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
        Schema::dropIfExists('ventas');
    }
};
