<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompraController extends Controller
{
    /**
     * Muestra la lista de compras y KPIs.
     */
    public function index(): View
    {
        // Se obtienen todas las compras con proveedor y empleado ordenadas por fecha descendente
        $compras = Compra::with(['proveedor', 'usuario'])->orderBy('fecha', 'desc')->get();

        // Se obtienen los proveedores y productos activos para los selectores del modal de compra
        $proveedores = Proveedor::orderBy('nombre', 'asc')->get();
        $productos = Producto::where('estado', 'Activo')->orderBy('nombre', 'asc')->get();

        // Cálculo de KPIs de compras
        $kpi = [
            'total' => Compra::count(),
            'monto' => Compra::sum('total'),
            'hoy' => Compra::whereDate('fecha', today())->count(),
        ];

        /** @var User $user */
        $user = Auth::user();
        if ($user->rol_id === 2) {
            return view('empleado.compras', compact('compras', 'proveedores', 'productos', 'kpi'));
        }

        return view('admin.compras', compact('compras', 'proveedores', 'productos', 'kpi'));
    }

    /**
     * Almacena una nueva compra en una transacción de base de datos.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'items' => ['required', 'string'], // Esperamos un JSON serializado del carrito de compras
        ], [
            'proveedor_id.required' => 'Selecciona un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'items.required' => 'Agrega al menos un producto al detalle.',
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'msg' => 'Agrega al menos un producto al detalle.']);
            }
            return redirect()->back()->withErrors(['items' => 'Agrega al menos un producto al detalle.']);
        }

        try {
            $idCompra = DB::transaction(function () use ($request, $items) {
                // 1. Calcular el total global de la compra
                $total = array_sum(array_map(fn($item) => $item['cantidad'] * $item['precio_unitario'], $items));

                // 2. Crear registro de compra
                $compra = Compra::create([
                    'fecha' => now(),
                    'total' => $total,
                    'proveedor_id' => $request->proveedor_id,
                    'usuario_id' => Auth::id(), // ID del usuario autenticado
                ]);

                // 3. Procesar cada ítem del detalle
                foreach ($items as $item) {
                    $subtotal = $item['cantidad'] * $item['precio_unitario'];

                    // Guardar en detalle_compras
                    DetalleCompra::create([
                        'compra_id' => $compra->id,
                        'producto_id' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $subtotal,
                    ]);

                    // Incrementar el stock del producto
                    $producto = Producto::lockForUpdate()->find($item['id_producto']);
                    $nuevoStock = $producto->stock + $item['cantidad'];
                    $producto->update(['stock' => $nuevoStock]);

                    // Registrar movimiento de Entrada en el Kardex (inventario)
                    Inventario::registrarMovimiento($producto->id, 'Entrada', $nuevoStock, $item['cantidad']);
                }

                return $compra->id;
            });

            return response()->json([
                'ok' => true,
                'msg' => 'Compra registrada correctamente.',
                'id_compra' => $idCompra
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al registrar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el detalle de una compra específica en formato JSON.
     */
    public function show(Compra $compra): JsonResponse
    {
        // Se cargan los detalles incluyendo la información del producto asociado
        $detalles = DetalleCompra::with('producto')
            ->where('compra_id', $compra->id)
            ->get()
            ->map(function ($d) {
                return [
                    'id_producto' => $d->producto_id,
                    'producto' => $d->producto->nombre ?? 'Producto Eliminado',
                    'talla' => $d->producto->talla ?? '—',
                    'color' => $d->producto->color ?? '—',
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'subtotal' => $d->subtotal,
                ];
            });

        return response()->json($detalles);
    }
}
