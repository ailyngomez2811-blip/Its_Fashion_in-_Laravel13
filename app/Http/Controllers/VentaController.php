<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VentaController extends Controller
{
    /**
     * Muestra el catálogo de ventas y KPIs.
     */
    public function index(): View
    {
        // Obtener las ventas con relaciones cargadas
        $ventas = Venta::with(['cliente', 'usuario'])->orderBy('fecha', 'desc')->get();

        // Obtener clientes activos (rol_id = 3 representa el rol Cliente en la base de datos)
        $clientes = User::where('rol_id', 3)->where('estado', 'Activo')->orderBy('nombre', 'asc')->get();

        // Obtener productos activos con stock
        $productos = Producto::where('estado', 'Activo')->orderBy('nombre', 'asc')->get();

        // Verificar si la caja está abierta
        $cajaAbierta = DB::table('cajas')
            ->where('estado', 'Abierta')
            ->exists();

        // KPIs de ventas
        $kpi = [
            'total' => Venta::count(),
            'monto' => Venta::where('estado', 'Completada')->sum('total'),
            'completadas' => Venta::where('estado', 'Completada')->count(),
            'hoy' => Venta::whereDate('fecha', today())->count(),
        ];

        return view('admin.ventas', compact('ventas', 'clientes', 'productos', 'cajaAbierta', 'kpi'));
    }

    /**
     * Almacena una nueva venta en una transacción de base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cliente_id' => ['nullable', 'exists:users,id'],
            'metodo_pago' => ['required', 'in:Efectivo,Transferencia bancaria'],
            'items' => ['required', 'string'], // Esperamos un JSON serializado de los ítems de venta
        ], [
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'El método de pago seleccionado es inválido.',
            'items.required' => 'Debes agregar al menos un producto al detalle de venta.',
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return response()->json(['ok' => false, 'msg' => 'Agrega al menos un producto a la venta.']);
        }

        try {
            $idVenta = DB::transaction(function () use ($request, $items) {
                // 1. Verificar stock disponible para todos los ítems antes de proceder
                foreach ($items as $item) {
                    $producto = Producto::find($item['id_producto']);
                    if (!$producto || $producto->estado !== 'Activo') {
                        throw new \Exception("El producto '{$item['nombre']}' ya no está disponible.");
                    }
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para '{$producto->nombre}' (Disponible: {$producto->stock}, Solicitado: {$item['cantidad']}).");
                    }
                }

                // 2. Calcular total de la venta
                $total = array_sum(array_map(fn($item) => $item['cantidad'] * $item['precio_unitario'], $items));

                // 3. Registrar venta
                $venta = Venta::create([
                    'fecha' => now(),
                    'total' => $total,
                    'cliente_id' => $request->cliente_id ? $request->cliente_id : null,
                    'metodo_pago' => $request->metodo_pago,
                    'estado' => 'Completada',
                    'usuario_id' => Auth::id(),
                ]);

                // 4. Registrar detalles y actualizar stock
                foreach ($items as $item) {
                    // Guardar detalle_ventas
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                    ]);

                    // Descontar stock del producto
                    $producto = Producto::lockForUpdate()->find($item['id_producto']);
                    $nuevoStock = $producto->stock - $item['cantidad'];
                    $producto->update(['stock' => $nuevoStock]);

                    // Registrar movimiento de Salida en el Kardex (inventario)
                    Inventario::registrarMovimiento($producto->id, 'Salida', $nuevoStock, $item['cantidad']);
                }

                // 5. Registrar en caja activa si existe una abierta
                $caja = DB::table('cajas')
                    ->where('estado', 'Abierta')
                    ->first();

                if ($caja) {
                    // Registrar el movimiento de ingreso de caja
                    DB::table('movimientos_caja')->insert([
                        'caja_id' => $caja->id,
                        'tipo' => 'Ingreso',
                        'monto' => $total,
                        'concepto' => "Venta #{$venta->id} ({$request->metodo_pago})",
                        'fecha' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Actualizar el total_ingresos de la caja activa
                    DB::table('cajas')
                        ->where('id', $caja->id)
                        ->update([
                            'total_ingresos' => DB::raw("COALESCE(total_ingresos, 0) + {$total}"),
                            'updated_at' => now(),
                        ]);
                }

                return $venta->id;
            });

            return response()->json([
                'ok' => true,
                'msg' => 'Venta registrada correctamente.',
                'id_venta' => $idVenta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra los detalles de una venta específica en formato JSON.
     */
    public function show(Venta $venta): JsonResponse
    {
        $detalles = DetalleVenta::with('producto')
            ->where('venta_id', $venta->id)
            ->get()
            ->map(function ($d) {
                return [
                    'id_producto' => $d->producto_id,
                    'producto' => $d->producto->nombre ?? 'Producto Eliminado',
                    'talla' => $d->producto->talla ?? '—',
                    'color' => $d->producto->color ?? '—',
                    'cantidad' => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'subtotal' => $d->cantidad * $d->precio_unitario,
                ];
            });

        return response()->json($detalles);
    }

    /**
     * Busca clientes activos para el autocompletado en el formulario de ventas.
     */
    public function buscarCliente(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clientes = User::where('rol_id', 3)
            ->where('estado', 'Activo')
            ->where(function($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('apellido', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'nombre', 'apellido', 'email']);

        return response()->json($clientes);
    }
}
