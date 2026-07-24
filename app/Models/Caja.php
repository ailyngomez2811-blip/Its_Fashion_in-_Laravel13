<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'cajas';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'total_ingresos',
        'total_egresos',
        'saldo_inicial',
        'saldo_final',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'justificacion',
        'estado',
        'usuario_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'fecha_apertura' => 'datetime',
            'fecha_cierre' => 'datetime',
        ];
    }

    /**
     * Relación: Una caja pertenece al usuario que la abre/administra.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una caja tiene muchos movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'caja_id');
    }
}
