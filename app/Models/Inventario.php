<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'inventarios';

    // Atributos asignables masivamente (fillable)
    protected $fillable = [
        'fecha_registro',
        'stock_disponible',
        'tipo_movimiento',
        'cantidad',
        'producto_id',
    ];

    // Castear atributos a tipos nativos
    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación: Un movimiento de inventario pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Registra un nuevo movimiento en el historial (Kardex).
     * Replicando la lógica de la función registrarMovimiento del proyecto original.
     */
    public static function registrarMovimiento($idProducto, $tipoMovimiento, $stockResultante, $cantidad = null)
    {
        // Si no se define la cantidad, se calcula comparando con el último movimiento registrado
        if ($cantidad === null) {
            $ultimo = self::where('producto_id', $idProducto)
                ->orderBy('id', 'desc')
                ->first();

            if ($ultimo) {
                $cantidad = abs($stockResultante - (int)$ultimo->stock_disponible);
            } else {
                $cantidad = $stockResultante;
            }
        }

        return self::create([
            'fecha_registro' => now(),
            'stock_disponible' => $stockResultante,
            'tipo_movimiento' => $tipoMovimiento,
            'producto_id' => $idProducto,
            'cantidad' => (int)$cantidad,
        ]);
    }
}
