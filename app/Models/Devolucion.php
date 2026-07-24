<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'devoluciones';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'venta_id',
        'fecha',
        'motivo',
        'total_devolucion',
        'usuario_id',
        'estado',
        'fecha_resolucion',
        'admin_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'fecha_resolucion' => 'datetime',
        ];
    }

    /**
     * Relación: Una devolución pertenece a una venta.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Relación: Una devolución pertenece al usuario que la solicita (User).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una devolución pertenece al administrador que la resuelve (User).
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relación: Una devolución tiene muchos detalles.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class, 'devolucion_id');
    }
}
