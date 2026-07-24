<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'productos';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_venta',
        'precio_compra',
        'stock',
        'stock_minimo',
        'talla',
        'color',
        'estado',
        'categoria_id',
    ];

    /**
     * Relación: Un producto pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación: Un producto puede tener muchos movimientos de inventario.
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'producto_id');
    }
}
