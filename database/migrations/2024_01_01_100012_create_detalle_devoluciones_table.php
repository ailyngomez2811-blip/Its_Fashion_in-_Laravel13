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
        Schema::create('detalle_devoluciones', function (Blueprint $table) {

            $table->id();

            // RELACIÓN CON DEVOLUCIÓN
            $table->foreignId('devolucion_id')
                ->nullable()
                ->constrained('devoluciones')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // RELACIÓN CON PRODUCTO
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // DATOS DEL DETALLE
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);

            // FECHAS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_devoluciones');
    }
};
