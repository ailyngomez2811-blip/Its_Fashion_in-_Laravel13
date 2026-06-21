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
        Schema::create('devoluciones', function (Blueprint $table) {

            $table->id();

            // RELACIÓN CON VENTA
            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // DATOS DE LA DEVOLUCIÓN
            $table->dateTime('fecha')->nullable();
            $table->text('motivo')->nullable();
            $table->decimal('total_devolucion', 10, 2);

            // RELACIÓN CON USUARIO (QUIÉN SOLICITA)
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // ESTADO Y RESOLUCIÓN
            $table->enum('estado', ['Pendiente', 'Aceptada', 'Rechazada'])
                ->default('Pendiente');
            $table->dateTime('fecha_resolucion')->nullable();

            // RELACIÓN CON USUARIO (ADMIN QUE RESUELVE)
            $table->foreignId('admin_id')
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
        Schema::dropIfExists('devoluciones');
    }
};
