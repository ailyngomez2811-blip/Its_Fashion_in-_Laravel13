<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'proveedores';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'documento',
    ];
}
