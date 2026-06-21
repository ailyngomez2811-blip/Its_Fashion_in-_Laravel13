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
        Schema::create('compras', function (Blueprint $table) {

            $table->id();

            // DATOS DE LA COMPRA
            $table->dateTime('fecha')->nullable();
            $table->decimal('total', 10, 2);

            // RELACIÓN CON PROVEEDOR
            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // RELACIÓN CON USUARIO (QUIÉN REGISTRÓ LA COMPRA)
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
        Schema::dropIfExists('compras');
    }
};
