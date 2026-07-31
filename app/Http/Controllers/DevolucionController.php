<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DevolucionController extends Controller
{
    /**
     * Muestra la lista de devoluciones y KPIs de resoluciones.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Si es cliente, redirigir a su vista específica mis_devoluciones
        if ($user->rol_id === 3) {
            $misDevoluciones = Devolucion::whereHas('venta', function ($q) use ($user) {
                    $q->where('cliente_id', $user->id);
                })
                ->orderBy('fecha', 'desc')
                ->get();
            $totalDevoluciones = $misDevoluciones->count();
            return view('cliente.mis_devoluciones', compact('misDevoluciones', 'totalDevoluciones'));
        }

        // Se cargan las devoluciones ordenadas por fecha descendente
        $devoluciones = Devolucion::with(['venta.cliente', 'usuario', 'admin'])
            ->orderBy('fecha', 'desc')
            ->get();

        // Cálculo de KPIs
        $kpi = [
            'total_monto' => Devolucion::where('estado', 'Aceptada')->sum('total_devolucion'),
            'pendientes' => Devolucion::where('estado', 'Pendiente')->count(),
        ];

        return view('admin.devoluciones', compact('devoluciones', 'kpi'));
    }

    /**
     * Registra una nueva solicitud de devolución en estado "Pendiente".
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'venta_id' => ['required', 'exists:ventas,id'],
            'motivo' => ['required', 'string', 'max:500'],
            'items' => ['required', 'string'], // JSON string de los items a devolver
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return response()->json(['ok' => false, 'msg' => 'Debes agregar al menos un producto para la devolución.']);
        }

        // Obtener venta original
        $venta = Venta::find($request->venta_id);
        if (!$venta) {
            return response()->json(['ok' => false, 'msg' => 'La venta no existe.']);
        }

        if ($venta->estado !== 'Completada') {
            return response()->json(['ok' => false, 'msg' => 'Solo se pueden solicitar devoluciones de ventas completadas.']);
        }

        try {
            $idDevolucion = DB::transaction(function () use ($request, $venta, $items) {
                // Obtener detalles de la venta para validar cantidad original
                $detallesVenta = DetalleVenta::where('venta_id', $venta->id)->get()->pluck('cantidad', 'producto_id')->toArray();

                $totalDevolucion = 0;

                // Validar cantidades devueltas contra la venta original
                foreach ($items as $item) {
                    $prodId = $item['id_producto'];
                    $qtyDevuelta = $item['cantidad'];

                    if (!isset($detallesVenta[$prodId]) || $qtyDevuelta > $detallesVenta[$prodId]) {
                        throw new \Exception("La cantidad a devolver de un artículo supera la cantidad comprada originalmente.");
                    }

                    // Obtener precio original de la venta
                    $detalleVentaOriginal = DetalleVenta::where('venta_id', $venta->id)->where('producto_id', $prodId)->first();
                    $precioOriginal = $detalleVentaOriginal->precio_unitario;

                    $totalDevolucion += $qtyDevuelta * $precioOriginal;
                }

                // Crear cabecera de Devolución
                $devolucion = Devolucion::create([
                    'venta_id' => $venta->id,
                    'fecha' => now(),
                    'motivo' => trim($request->motivo),
                    'total_devolucion' => $totalDevolucion,
                    'usuario_id' => Auth::id(),
                    'estado' => 'Pendiente',
                ]);

                // Registrar los ítems en detalle_devoluciones
                foreach ($items as $item) {
                    $prodId = $item['id_producto'];
                    $qtyDevuelta = $item['cantidad'];

                    $detalleVentaOriginal = DetalleVenta::where('venta_id', $venta->id)->where('producto_id', $prodId)->first();
                    $precioOriginal = $detalleVentaOriginal->precio_unitario;

                    DetalleDevolucion::create([
                        'devolucion_id' => $devolucion->id,
                        'producto_id' => $prodId,
                        'cantidad' => $qtyDevuelta,
                        'precio_unitario' => $precioOriginal,
                    ]);
                }

                return $devolucion->id;
            });

            return response()->json([
                'ok' => true,
                'msg' => 'Solicitud de devolución registrada correctamente.',
                'id_devolucion' => $idDevolucion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al procesar devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna el detalle de los productos en una devolución específica.
     */
    public function show(Devolucion $devolucion): JsonResponse
    {
        $detalles = DetalleDevolucion::with('producto')
            ->where('devolucion_id', $devolucion->id)
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

        return response()->json([
            'venta_id' => $devolucion->venta_id,
            'motivo' => $devolucion->motivo,
            'total_devolucion' => $devolucion->total_devolucion,
            'estado' => $devolucion->estado,
            'items' => $detalles
        ]);
    }

    /**
     * Aprueba la solicitud de devolución (Suma stock, Kardex, contabilidad en caja).
     */
    public function aprobar(Devolucion $devolucion): JsonResponse
    {
        if (Auth::user()->rol_id !== 1) {
            return response()->json(['ok' => false, 'msg' => 'Solo el Administrador está autorizado para resolver devoluciones.'], 403);
        }

        if ($devolucion->estado !== 'Pendiente') {
            return response()->json(['ok' => false, 'msg' => 'Esta devolución ya ha sido resuelta o no existe.']);
        }

        try {
            DB::transaction(function () use ($devolucion) {
                // 1. Marcar como aceptada
                $devolucion->update([
                    'estado' => 'Aceptada',
                    'fecha_resolucion' => now(),
                    'admin_id' => Auth::id(),
                ]);

                // 2. Retornar el stock a los productos e ingresar al Kardex
                $detalles = DetalleDevolucion::where('devolucion_id', $devolucion->id)->get();
                foreach ($detalles as $item) {
                    $producto = Producto::lockForUpdate()->find($item->producto_id);
                    $nuevoStock = $producto->stock + $item->cantidad;
                    $producto->update(['stock' => $nuevoStock]);

                    // Kardex: Entrada por devolución
                    Inventario::registrarMovimiento($producto->id, 'Entrada', $nuevoStock, $item->cantidad);
                }

                // 3. Contabilizar egreso si hay una caja abierta
                $caja = DB::table('cajas')->where('estado', 'Abierta')->first();
                if ($caja) {
                    $concepto = "Devolución #{$devolucion->id} (Venta #{$devolucion->venta_id})";

                    // Registrar egreso
                    DB::table('movimientos_caja')->insert([
                        'caja_id' => $caja->id,
                        'tipo' => 'Egreso',
                        'monto' => $devolucion->total_devolucion,
                        'concepto' => $concepto,
                        'fecha' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Actualizar egresos de la caja activa
                    DB::table('cajas')
                        ->where('id', $caja->id)
                        ->update([
                            'total_egresos' => DB::raw("COALESCE(total_egresos, 0) + {$devolucion->total_devolucion}"),
                            'updated_at' => now(),
                        ]);
                }
            });

            return response()->json(['ok' => true, 'msg' => 'La devolución ha sido aprobada correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'msg' => 'Error al aprobar la devolución: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rechaza la solicitud de devolución (No afecta stock ni caja).
     */
    public function rechazar(Devolucion $devolucion): JsonResponse
    {
        if (Auth::user()->rol_id !== 1) {
            return response()->json(['ok' => false, 'msg' => 'Solo el Administrador está autorizado para resolver devoluciones.'], 403);
        }

        if ($devolucion->estado !== 'Pendiente') {
            return response()->json(['ok' => false, 'msg' => 'Esta devolución ya ha sido resuelta o no existe.']);
        }

        try {
            $devolucion->update([
                'estado' => 'Rechazada',
                'fecha_resolucion' => now(),
                'admin_id' => Auth::id(),
            ]);

            return response()->json(['ok' => true, 'msg' => 'La solicitud de devolución ha sido rechazada correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'msg' => 'Error al rechazar la devolución: ' . $e->getMessage()], 500);
        }
    }
}
