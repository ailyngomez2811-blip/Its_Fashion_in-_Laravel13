<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'compras';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'fecha',
        'total',
        'proveedor_id',
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
     * Relación: Una compra pertenece a un proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Relación: Una compra pertenece a un usuario (empleado).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una compra tiene muchos detalles.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }
}
