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
        Schema::create('proveedores', function (Blueprint $table) {

            $table->id();

            // DATOS DEL PROVEEDOR
            $table->string('nombre', 100);
            $table->string('contacto', 100);
            $table->string('telefono', 15);
            $table->string('email', 100)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('documento', 20)->unique();

            // FECHAS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
