<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venta;
use App\Models\Devolucion;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController extends Controller
{
    /**
     * Muestra el panel de reportes con KPIs y gráficos.
     */
    public function index(Request $request): View
    {
        $periodo = $request->get('periodo', 'Este mes');
        $desde = $request->get('desde', '');
        $hasta = $request->get('hasta', '');

        if (empty($desde) || empty($hasta)) {
            if ($periodo === 'Esta semana') {
                $desde = now()->startOfWeek()->format('Y-m-d');
                $hasta = now()->endOfWeek()->format('Y-m-d');
            } elseif ($periodo === 'Este mes') {
                $desde = now()->startOfMonth()->format('Y-m-d');
                $hasta = now()->endOfMonth()->format('Y-m-d');
            } elseif ($periodo === 'Últimos 3 meses') {
                $desde = now()->subMonths(3)->format('Y-m-d');
                $hasta = now()->format('Y-m-d');
            } elseif ($periodo === 'Este año') {
                $desde = now()->startOfYear()->format('Y-m-d');
                $hasta = now()->endOfYear()->format('Y-m-d');
            } else {
                $periodo = 'Este mes';
                $desde = now()->startOfMonth()->format('Y-m-d');
                $hasta = now()->endOfMonth()->format('Y-m-d');
            }
        } else {
            $periodo = 'Personalizado';
        }

        // 1. Ventas del período por día o mes (para la gráfica principal)
        $diff = (strtotime($hasta) - strtotime($desde)) / 86400;
        $ventas_semana = [];
        $dias = [];
        $label_grafica = "";

        if ($diff <= 31) {
            $rows = Venta::select(DB::raw('DATE(fecha) as punto'), DB::raw('SUM(total) as total'))
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->groupBy(DB::raw('DATE(fecha)'))
                ->orderBy('punto', 'asc')
                ->get()
                ->pluck('total', 'punto')
                ->toArray();

            for ($d = strtotime($desde); $d <= strtotime($hasta); $d = strtotime("+1 day", $d)) {
                $fecha = date('Y-m-d', $d);
                $ventas_semana[] = (float)($rows[$fecha] ?? 0);
                $dias[] = date('d/m', $d);
            }
            $label_grafica = "Ventas por día";
        } else {
            $rows = Venta::select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as punto"), DB::raw('SUM(total) as total'))
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->groupBy('punto')
                ->orderBy('punto', 'asc')
                ->get()
                ->pluck('total', 'punto')
                ->toArray();

            $start = new \DateTime($desde);
            $end = new \DateTime($hasta);
            $end->modify('+1 month');
            $interval = new \DateInterval('P1M');
            $periodObj = new \DatePeriod($start, $interval, $end);

            foreach ($periodObj as $dt) {
                $mes = $dt->format('Y-m');
                $ventas_semana[] = (float)($rows[$mes] ?? 0);
                $dias[] = $dt->format('m/Y');
            }
            $label_grafica = "Ventas por mes";
        }

        // 2. Ventas últimos 6 meses (para tendencia)
        $rows2 = Venta::select(DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes"), DB::raw('SUM(total) as total'))
            ->where('estado', 'Completada')
            ->where('fecha', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get()
            ->pluck('total', 'mes')
            ->toArray();

        $ventas_mes = [];
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $ventas_mes[] = (float)($rows2[$key] ?? 0);
            $meses[] = now()->subMonths($i)->translatedFormat('M');
        }

        // 3. KPIs generales
        $kpi = Venta::select(
                DB::raw('COALESCE(SUM(total), 0) as total_periodo'),
                DB::raw('COUNT(*) as transacciones'),
                DB::raw('COALESCE(AVG(total), 0) as ticket_promedio'),
                DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Efectivo' THEN total ELSE 0 END), 0) as efectivo"),
                DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Transferencia' THEN total ELSE 0 END), 0) as transferencia")
            )
            ->where('estado', 'Completada')
            ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
            ->first();

        // 4. Clientes únicos activos
        $total_clientes = Venta::where('estado', 'Completada')
            ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
            ->distinct('cliente_id')
            ->count('cliente_id');

        // 5. Top 5 productos más vendidos
        $top_productos = DetalleVenta::select('productos.nombre', 'productos.talla', 'productos.color',
                DB::raw('SUM(detalle_ventas.cantidad) as vendidos'),
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos')
            )
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->where('ventas.estado', 'Completada')
            ->whereBetween(DB::raw('DATE(ventas.fecha)'), [$desde, $hasta])
            ->groupBy('detalle_ventas.producto_id', 'productos.nombre', 'productos.talla', 'productos.color')
            ->orderBy('vendidos', 'desc')
            ->limit(5)
            ->get();

        // 6. Últimas devoluciones del período
        $devols = Devolucion::select('devoluciones.*', 'users.nombre as cliente_nombre', 'users.apellido as cliente_apellido')
            ->join('ventas', 'ventas.id', '=', 'devoluciones.venta_id')
            ->leftJoin('users', 'users.id', '=', 'ventas.cliente_id')
            ->whereBetween(DB::raw('DATE(devoluciones.fecha)'), [$desde, $hasta])
            ->orderBy('devoluciones.fecha', 'desc')
            ->limit(8)
            ->get();

        // 7. Inventario actual consolidado
        $inv = Producto::with('categoria')->get();

        return view('admin.reportes', compact(
            'periodo', 'desde', 'hasta', 'ventas_semana', 'dias', 'label_grafica',
            'ventas_mes', 'meses', 'kpi', 'total_clientes', 'top_productos', 'devols', 'inv'
        ));
    }

    /**
     * Exporta el reporte consolidado en PDF.
     */
    public function exportarPdf(Request $request)
    {
        $incluirVentas = $request->has('Ventas');
        $incluirInventario = $request->has('Inventario');
        $incluirProductos = $request->has('Productos_mas_vendidos');
        $incluirDevoluciones = $request->has('Devoluciones');

        $desde = $request->get('desde', now()->subDays(6)->format('Y-m-d'));
        $hasta = $request->get('hasta', now()->format('Y-m-d'));

        // Si no hay nada seleccionado, por defecto mostramos todo
        if (!$incluirVentas && !$incluirInventario && !$incluirProductos && !$incluirDevoluciones) {
            $incluirVentas = true;
            $incluirInventario = true;
            $incluirProductos = true;
            $incluirDevoluciones = true;
        }

        // Consultas
        $ventas = [];
        $kpi = null;
        if ($incluirVentas) {
            $ventas = Venta::with('cliente')
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->orderBy('fecha', 'desc')
                ->get();

            $kpi = Venta::select(
                    DB::raw('COALESCE(SUM(total), 0) as total_periodo'),
                    DB::raw('COUNT(*) as transacciones'),
                    DB::raw('COALESCE(AVG(total), 0) as ticket_promedio'),
                    DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Efectivo' THEN total ELSE 0 END), 0) as efectivo"),
                    DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Transferencia' THEN total ELSE 0 END), 0) as transferencia")
                )
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->first();
        }

        $inventario = [];
        if ($incluirInventario) {
            $inventario = Producto::with('categoria')->get();
        }

        $productos = [];
        if ($incluirProductos) {
            $productos = DetalleVenta::select('productos.nombre', 'productos.talla', 'productos.color',
                    DB::raw('SUM(detalle_ventas.cantidad) as vendidos'),
                    DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos')
                )
                ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
                ->where('ventas.estado', 'Completada')
                ->whereBetween(DB::raw('DATE(ventas.fecha)'), [$desde, $hasta])
                ->groupBy('detalle_ventas.producto_id', 'productos.nombre', 'productos.talla', 'productos.color')
                ->orderBy('vendidos', 'desc')
                ->get();
        }

        $devoluciones = [];
        if ($incluirDevoluciones) {
            $devoluciones = Devolucion::select('devoluciones.*', 'users.nombre as cliente_nombre', 'users.apellido as cliente_apellido')
                ->join('ventas', 'ventas.id', '=', 'devoluciones.venta_id')
                ->leftJoin('users', 'users.id', '=', 'ventas.cliente_id')
                ->whereBetween(DB::raw('DATE(devoluciones.fecha)'), [$desde, $hasta])
                ->orderBy('devoluciones.fecha', 'desc')
                ->get();
        }

        $html = view('admin.reportes_pdf', compact(
            'desde', 'hasta', 'incluirVentas', 'incluirInventario', 'incluirProductos', 'incluirDevoluciones',
            'ventas', 'kpi', 'inventario', 'productos', 'devoluciones'
        ))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Reporte_Its_Fashion_' . date('Ymd_Hi') . '.pdf"');
    }

    /**
     * Exporta el reporte consolidado en XLS (Excel).
     */
    public function exportarExcel(Request $request)
    {
        $incluirVentas = $request->has('Ventas');
        $incluirInventario = $request->has('Inventario');
        $incluirProductos = $request->has('Productos_más_vendidos') || $request->has('Productos_mas_vendidos');
        $incluirDevoluciones = $request->has('Devoluciones');

        $desde = $request->get('desde', now()->subDays(6)->format('Y-m-d'));
        $hasta = $request->get('hasta', now()->format('Y-m-d'));

        if (!$incluirVentas && !$incluirInventario && !$incluirProductos && !$incluirDevoluciones) {
            $incluirVentas = true;
            $incluirInventario = true;
            $incluirProductos = true;
            $incluirDevoluciones = true;
        }

        // Consultas
        $ventas = [];
        $kpi = null;
        if ($incluirVentas) {
            $ventas = Venta::with('cliente')
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->orderBy('fecha', 'desc')
                ->get();

            $kpi = Venta::select(
                    DB::raw('COALESCE(SUM(total), 0) as total_periodo'),
                    DB::raw('COUNT(*) as transacciones'),
                    DB::raw('COALESCE(AVG(total), 0) as ticket_promedio'),
                    DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Efectivo' THEN total ELSE 0 END), 0) as efectivo"),
                    DB::raw("COALESCE(SUM(CASE WHEN metodo_pago='Transferencia' THEN total ELSE 0 END), 0) as transferencia")
                )
                ->where('estado', 'Completada')
                ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
                ->first();
        }

        $inventario = [];
        if ($incluirInventario) {
            $inventario = Producto::with('categoria')->get();
        }

        $productos = [];
        if ($incluirProductos) {
            $productos = DetalleVenta::select('productos.nombre', 'productos.talla', 'productos.color',
                    DB::raw('SUM(detalle_ventas.cantidad) as vendidos'),
                    DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos')
                )
                ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
                ->where('ventas.estado', 'Completada')
                ->whereBetween(DB::raw('DATE(ventas.fecha)'), [$desde, $hasta])
                ->groupBy('detalle_ventas.producto_id', 'productos.nombre', 'productos.talla', 'productos.color')
                ->orderBy('vendidos', 'desc')
                ->get();
        }

        $devoluciones = [];
        if ($incluirDevoluciones) {
            $devoluciones = Devolucion::select('devoluciones.*', 'users.nombre as cliente_nombre', 'users.apellido as cliente_apellido')
                ->join('ventas', 'ventas.id', '=', 'devoluciones.venta_id')
                ->leftJoin('users', 'users.id', '=', 'ventas.cliente_id')
                ->whereBetween(DB::raw('DATE(devoluciones.fecha)'), [$desde, $hasta])
                ->orderBy('devoluciones.fecha', 'desc')
                ->get();
        }

        $html = view('admin.reportes_excel', compact(
            'desde', 'hasta', 'incluirVentas', 'incluirInventario', 'incluirProductos', 'incluirDevoluciones',
            'ventas', 'kpi', 'inventario', 'productos', 'devoluciones'
        ))->render();

        // Configurar respuesta de descarga XLS
        $responseContent = "\xEF\xBB\xBF" . $html; // BOM para soporte UTF-8 en Excel
        
        return response($responseContent, 200)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="Reporte_Its_Fashion_' . date('Ymd_Hi') . '.xls"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
