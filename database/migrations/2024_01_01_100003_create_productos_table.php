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
        Schema::create('productos', function (Blueprint $table) {

            $table->id();

            // DATOS DEL PRODUCTO
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_compra', 10, 2);
            $table->integer('stock');
            $table->integer('stock_minimo')->nullable();
            $table->string('talla', 10);
            $table->string('color', 30);

            // ESTADO DEL PRODUCTO
            $table->enum('estado', ['Activo', 'Inactivo'])->nullable();

            // RELACIÓN CON CATEGORÍAS
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
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
        Schema::dropIfExists('productos');
    }
};
