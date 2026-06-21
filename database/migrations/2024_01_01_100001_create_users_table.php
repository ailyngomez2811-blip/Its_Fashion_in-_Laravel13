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
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            // RELACIÓN CON ROL
            $table->foreignId('rol_id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // DATOS PERSONALES
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('username', 50)->unique();
            $table->string('telefono', 15);

            // LOGIN
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // ESTADO DEL USUARIO
            $table->enum('estado', ['Activo', 'Inactivo'])
                ->default('Activo');

            // FECHA DE REGISTRO (dato de negocio, distinto de created_at)
            $table->timestamp('fecha_registro')->useCurrent();

            // TOKEN RECORDAR SESIÓN
            $table->rememberToken();

            // FECHAS
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET TOKENS
        |--------------------------------------------------------------------------
        */

        Schema::create('password_reset_tokens', function (Blueprint $table) {

            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | SESSIONS
        |--------------------------------------------------------------------------
        */

        Schema::create('sessions', function (Blueprint $table) {

            $table->string('id')->primary();

            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
