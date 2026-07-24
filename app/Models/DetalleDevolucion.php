<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'detalle_devoluciones';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'devolucion_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
    ];

    /**
     * Relación: Un detalle de devolución pertenece a una devolución.
     */
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id');
    }

    /**
     * Relación: Un detalle de devolución pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
