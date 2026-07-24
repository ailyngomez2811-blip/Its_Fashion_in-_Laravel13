<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'ventas';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'fecha',
        'total',
        'cliente_id',
        'metodo_pago',
        'estado',
        'usuario_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
        ];
    }

    /**
     * Relación: Una venta pertenece a un cliente (User).
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Relación: Una venta pertenece a un usuario/empleado (User).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una venta tiene muchos detalles.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
