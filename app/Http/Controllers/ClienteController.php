<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venta;
use App\Models\Devolucion;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    /**
     * Muestra el directorio de clientes y KPIs.
     */
    public function index(): View
    {
        // Obtener usuarios con rol_id = 3 (Clientes) con conteo de ventas y devoluciones asociadas
        $clientes = User::where('rol_id', 3)
            ->withCount(['ventas', 'devoluciones'])
            ->orderBy('created_at', 'desc')
            ->get();

        // KPIs de clientes
        $kpi = [
            'total' => $clientes->count(),
            'activos' => $clientes->where('estado', 'Activo')->count(),
            'compras' => $clientes->sum('ventas_count'),
            'devoluciones' => $clientes->sum('devoluciones_count'),
        ];

        return view('admin.clientes', compact('clientes', 'kpi'));
    }

    /**
     * Alterna el estado (Activo/Inactivo) de un cliente.
     */
    public function toggleEstado(User $cliente): JsonResponse
    {
        if ($cliente->rol_id !== 3) {
            return response()->json(['ok' => false, 'msg' => 'El usuario seleccionado no es un cliente.'], 403);
        }

        try {
            $nuevoEstado = $cliente->estado === 'Activo' ? 'Inactivo' : 'Activo';
            $cliente->update(['estado' => $nuevoEstado]);

            return response()->json([
                'ok' => true,
                'msg' => 'El estado del cliente ha sido actualizado a ' . $nuevoEstado . '.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna el historial de compras de un cliente específico.
     */
    public function compras(User $cliente): JsonResponse
    {
        if ($cliente->rol_id !== 3) {
            return response()->json(['ok' => false, 'msg' => 'No autorizado.'], 403);
        }

        // Obtener las ventas asociadas al cliente
        $compras = Venta::where('cliente_id', $cliente->id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($c) {
                // Desglosar los productos de la venta en un string
                $productosStr = DetalleVenta::where('venta_id', $c->id)
                    ->with('producto')
                    ->get()
                    ->map(fn($d) => ($d->producto->nombre ?? 'Producto Eliminado') . ' × ' . $d->cantidad)
                    ->implode(', ');

                return [
                    'id_venta' => $c->id,
                    'fecha' => $c->fecha ? $c->fecha->format('Y-m-d H:i') : '—',
                    'total' => $c->total,
                    'metodo_pago' => $c->metodo_pago,
                    'estado' => $c->estado,
                    'productos' => $productosStr ?: 'Sin detalle'
                ];
            });

        return response()->json($compras);
    }

    /**
     * Retorna el historial de devoluciones de un cliente específico.
     */
    public function devoluciones(User $cliente): JsonResponse
    {
        if ($cliente->rol_id !== 3) {
            return response()->json(['ok' => false, 'msg' => 'No autorizado.'], 403);
        }

        // Obtener las devoluciones asociadas a las ventas del cliente
        $devoluciones = Devolucion::whereHas('venta', function ($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($d) {
                return [
                    'id_devolucion' => $d->id,
                    'id_venta' => $d->venta_id,
                    'fecha' => $d->fecha ? $d->fecha->format('Y-m-d H:i') : '—',
                    'motivo' => $d->motivo,
                    'total_devolucion' => $d->total_devolucion,
                    'estado' => $d->estado
                ];
            });

        return response()->json($devoluciones);
    }
}
