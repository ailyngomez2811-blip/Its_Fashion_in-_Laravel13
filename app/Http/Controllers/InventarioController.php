<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventarioController extends Controller
{
    /**
     * Muestra el estado del inventario actual y el Kardex de movimientos.
     */
    public function index(): View
    {
        // 1. Obtener todos los productos con su categoría asociada
        $productos = Producto::with('categoria')->get();

        // 2. Obtener todas las categorías para los filtros
        $categorias = Categoria::all();

        // 3. Obtener el historial del Kardex (últimos 200 movimientos)
        $historial = Inventario::with('producto')
            ->orderBy('fecha_registro', 'desc')
            ->take(200)
            ->get();

        // 4. Calcular KPIs de stock
        $total = $productos->count();
        $conStock = $productos->filter(fn($p) => $p->stock > ($p->stock_minimo ?? 0))->count();
        $critico = $productos->filter(fn($p) => $p->stock > 0 && $p->stock <= ($p->stock_minimo ?? 0))->count();
        $sinStock = $productos->filter(fn($p) => $p->stock == 0)->count();

        $kpis = [
            'total' => $total,
            'conStock' => $conStock,
            'critico' => $critico,
            'sinStock' => $sinStock
        ];

        /** @var User $user */
        $user = Auth::user();
        if ($user->rol_id === 2) {
            return view('empleado.inventario', compact('productos', 'categorias', 'historial', 'kpis'));
        }

        return view('admin.inventario', compact('productos', 'categorias', 'historial', 'kpis'));
    }
}
