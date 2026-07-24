<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Muestra la lista de productos y estadísticas (KPIs).
     */
    public function index(): View
    {
        // Se obtienen todos los productos con su respectiva categoría ordenados por nombre
        $productos = Producto::with('categoria')->orderBy('nombre', 'asc')->get();

        // Se obtienen todas las categorías para el filtro y selección del formulario
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        // Cálculo de KPIs replicando fielmente la lógica nativa del proyecto anterior
        $kpi = [
            'total' => Producto::count(),
            'activos' => Producto::where('estado', 'Activo')->count(),
            'agotados' => Producto::where('stock', 0)->count(),
            'criticos' => Producto::whereRaw('stock > 0 AND stock <= stock_minimo')->count(),
        ];

        return view('admin.productos', compact('productos', 'categorias', 'kpi'));
    }

    /**
     * Almacena un nuevo producto y registra el movimiento de inventario inicial.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'precio_venta' => ['required', 'numeric', 'gt:0'],
            'precio_compra' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'talla' => ['required', 'string', 'max:10'],
            'color' => ['required', 'string', 'max:30'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'talla.required' => 'La talla es obligatoria.',
            'color.required' => 'El color es obligatorio.',
            'precio_compra.gt' => 'El precio de compra debe ser un valor positivo.',
            'precio_venta.gt' => 'El precio de venta debe ser un valor positivo.',
            'stock.min' => 'El stock no puede ser negativo.',
        ]);

        // Validación lógica: Precio de venta debe ser mayor al precio de compra
        if ((float)$request->precio_venta <= (float)$request->precio_compra) {
            return redirect()->back()
                ->withErrors(['precio_venta' => 'El precio de venta debe ser mayor al precio de compra.'])
                ->withInput();
        }

        // Crear producto
        $producto = Producto::create([
            'nombre' => trim($request->nombre),
            'descripcion' => trim($request->descripcion),
            'precio_venta' => $request->precio_venta,
            'precio_compra' => $request->precio_compra,
            'stock' => $request->stock,
            'stock_minimo' => $request->stock_minimo ?? 0,
            'talla' => $request->talla,
            'color' => trim($request->color),
            'estado' => $request->estado,
            'categoria_id' => $request->categoria_id,
        ]);

        // Registrar movimiento de Entrada inicial en el inventario/kardex
        Inventario::registrarMovimiento($producto->id, 'Entrada', $producto->stock, $producto->stock);

        return redirect()->route('productos.index')
            ->with('toast', [
                'type' => 'success',
                'text' => 'Producto creado correctamente.'
            ]);
    }

    /**
     * Obtiene los detalles de un producto en formato JSON (para edición AJAX).
     */
    public function show(Producto $producto): JsonResponse
    {
        return response()->json($producto);
    }

    /**
     * Actualiza un producto existente y registra el ajuste de inventario si cambia el stock.
     */
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'precio_venta' => ['required', 'numeric', 'gt:0'],
            'precio_compra' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'talla' => ['required', 'string', 'max:10'],
            'color' => ['required', 'string', 'max:30'],
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            'categoria_id' => ['required', 'exists:categorias,id'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'talla.required' => 'La talla es obligatoria.',
            'color.required' => 'El color es obligatorio.',
            'precio_compra.gt' => 'El precio de compra debe ser un valor positivo.',
            'precio_venta.gt' => 'El precio de venta debe ser un valor positivo.',
            'stock.min' => 'El stock no puede ser negativo.',
        ]);

        // Validación lógica: Precio de venta debe ser mayor al precio de compra
        if ((float)$request->precio_venta <= (float)$request->precio_compra) {
            return redirect()->back()
                ->withErrors(['precio_venta' => 'El precio de venta debe ser mayor al precio de compra.'])
                ->withInput();
        }

        $oldStock = (int)$producto->stock;
        $newStock = (int)$request->stock;
        $diff = $newStock - $oldStock;

        // Actualizar datos del producto
        $producto->update([
            'nombre' => trim($request->nombre),
            'descripcion' => trim($request->descripcion),
            'precio_venta' => $request->precio_venta,
            'precio_compra' => $request->precio_compra,
            'stock' => $newStock,
            'stock_minimo' => $request->stock_minimo ?? 0,
            'talla' => $request->talla,
            'color' => trim($request->color),
            'estado' => $request->estado,
            'categoria_id' => $request->categoria_id,
        ]);

        // Si el stock ha variado, se registra un movimiento automático en el Kardex
        if ($diff !== 0) {
            $tipo = $diff > 0 ? 'Entrada' : 'Salida';
            Inventario::registrarMovimiento($producto->id, $tipo, $newStock, abs($diff));
        }

        return redirect()->route('productos.index')
            ->with('toast', [
                'type' => 'success',
                'text' => 'Producto actualizado correctamente.'
            ]);
    }

    /**
     * Alterna el estado (Activo/Inactivo) de un producto vía AJAX/Fetch.
     */
    public function toggleEstado(Request $request, Producto $producto): JsonResponse
    {
        $request->validate([
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])]
        ]);

        $producto->update([
            'estado' => $request->estado
        ]);

        return response()->json(['ok' => true]);
    }
}
