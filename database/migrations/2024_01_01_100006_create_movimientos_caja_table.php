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
        Schema::create('movimientos_caja', function (Blueprint $table) {

            $table->id();

            // RELACIÓN CON CAJA
            $table->foreignId('caja_id')
                ->nullable()
                ->constrained('cajas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // DATOS DEL MOVIMIENTO
            $table->enum('tipo', ['Ingreso', 'Egreso'])->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('concepto', 200)->nullable();
            $table->dateTime('fecha')->nullable();

            // FECHAS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
