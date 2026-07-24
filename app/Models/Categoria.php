<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Nombre de la tabla asociada en la base de datos
    protected $table = 'categorias';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación: Una categoría tiene muchos productos.
     * (Se activará por completo cuando se cree el modelo Producto)
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
