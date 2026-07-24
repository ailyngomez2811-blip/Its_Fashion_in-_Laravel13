<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'movimientos_caja';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'caja_id',
        'tipo',
        'monto',
        'concepto',
        'fecha',
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
     * Relación: Un movimiento pertenece a una caja.
     */
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }
}
