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
        Schema::create('inventarios', function (Blueprint $table) {

            $table->id();

            // DATOS DEL MOVIMIENTO DE INVENTARIO
            $table->dateTime('fecha_registro')->nullable();
            $table->integer('stock_disponible');
            $table->enum('tipo_movimiento', ['Entrada', 'Salida', 'Ajuste'])->nullable();
            $table->integer('cantidad')->default(0);

            // RELACIÓN CON PRODUCTO
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
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
        Schema::dropIfExists('inventarios');
    }
};
