<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venta;
use App\Models\Devolucion;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

        // 8. Compras/Abastecimiento del período para Reportes de Proveedor (Escenario 3)
        $comprasPeriodo = \App\Models\Compra::with(['proveedor', 'usuario'])
            ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
            ->orderBy('fecha', 'desc')
            ->get();

        $totalInversionCompras = $comprasPeriodo->sum('total');

        return view('admin.reportes', compact(
            'periodo', 'desde', 'hasta', 'ventas_semana', 'dias', 'label_grafica',
            'ventas_mes', 'meses', 'kpi', 'total_clientes', 'top_productos', 'devols', 'inv',
            'comprasPeriodo', 'totalInversionCompras'
        ));
    }

    /**
     * Exporta el reporte consolidado en PDF.
     */
    public function exportarPdf(Request $request)
    {
        $incluirVentas = $request->has('Ventas');
        $incluirInventario = $request->has('Inventario');
        $incluirProductos = $request->has('Productos_mas_vendidos') || $request->has('Productos_más_vendidos') || $request->has('Productos') || $request->has('productos_mas_vendidos');
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

        $logoPath = public_path('img/icono head .png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $logoNombrePath = public_path('img/logo en nombre.png');
        $logoNombreBase64 = '';
        if (file_exists($logoNombrePath)) {
            $logoNombreBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoNombrePath));
        }

        $html = view('admin.reportes_pdf', compact(
            'desde', 'hasta', 'incluirVentas', 'incluirInventario', 'incluirProductos', 'incluirDevoluciones',
            'ventas', 'kpi', 'inventario', 'productos', 'devoluciones', 'logoBase64', 'logoNombreBase64'
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
        $incluirProductos = $request->has('Productos_mas_vendidos') || $request->has('Productos_más_vendidos') || $request->has('Productos') || $request->has('productos_mas_vendidos');
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

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remover hoja por defecto

        $borderBlack = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $headerStyle = [
            'font' => ['name' => 'Times New Roman', 'size' => 12, 'bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $userName = trim((Auth::user()?->nombre ?? 'Admin') . ' ' . (Auth::user()?->apellido ?? 'General'));
        $fechaGen = now()->format('d/m/Y H:i');
        $rangoTexto = 'Rango de evaluación: ' . date('d/m/Y', strtotime($desde)) . ' al ' . date('d/m/Y', strtotime($hasta));
        $logoPath = public_path('img/logo en nombre.png');

        // ==================== HOJA 1: VENTAS ====================
        if ($incluirVentas) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Ventas e Ingresos');
            $sheet->setShowGridLines(false);

            // Fila 1: Vacía
            $sheet->getRowDimension(1)->setRowHeight(15);

            // Fila 2: Logo y Marca
            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A2');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(0);
                $drawing->setWorksheet($sheet);
            }

            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $run1 = $richText->createTextRun('Its ');
            $run1->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('000000');
            $run2 = $richText->createTextRun('Fashion');
            $run2->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->setCellValue('B2', $richText);
            $sheet->mergeCells('B2:D2');
            $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(36);

            $sheet->setCellValue('E2', 'Generado por: ' . $userName);
            $sheet->mergeCells('E2:F2');
            $sheet->getStyle('E2')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Fila 3: Subtítulo y Fecha
            $sheet->setCellValue('A3', 'Consolidado de Ventas');
            $sheet->mergeCells('A3:D3');
            $sheet->getStyle('A3')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('000000');
            $sheet->getRowDimension(3)->setRowHeight(24);

            $sheet->setCellValue('E3', 'Fecha: ' . $fechaGen);
            $sheet->mergeCells('E3:F3');
            $sheet->getStyle('E3')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Fila 4: Rango
            $sheet->setCellValue('A4', $rangoTexto);
            $sheet->mergeCells('A4:F4');
            $sheet->getStyle('A4')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getRowDimension(4)->setRowHeight(20);

            // Fila 5: Vacía
            $sheet->getRowDimension(5)->setRowHeight(10);

            // Fila 6: Sección
            $sheet->setCellValue('A6', 'RESUMEN E INGRESOS DEL PERIODO');
            $sheet->mergeCells('A6:F6');
            $sheet->getStyle('A6')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->getStyle('A6:F6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)->getColor()->setRGB('2563EB');
            $sheet->getRowDimension(6)->setRowHeight(24);

            // Fila 7: Vacía
            $sheet->getRowDimension(7)->setRowHeight(10);

            // Fila 8: KPIs Header
            $sheet->setCellValue('A8', 'TOTAL INGRESOS');
            $sheet->setCellValue('B8', 'VENTAS');
            $sheet->setCellValue('C8', 'TICKET PROMEDIO');
            $sheet->mergeCells('C8:D8');
            $sheet->setCellValue('E8', 'EFECTIVO');
            $sheet->mergeCells('E8:F8');

            $sheet->getStyle('A8:F8')->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 12, 'bold' => true, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A8')->applyFromArray($borderBlack);
            $sheet->getStyle('B8')->applyFromArray($borderBlack);
            $sheet->getStyle('C8:D8')->applyFromArray($borderBlack);
            $sheet->getStyle('E8:F8')->applyFromArray($borderBlack);
            $sheet->getRowDimension(8)->setRowHeight(24);

            // Fila 9: KPIs Values
            $sheet->setCellValue('A9', $kpi->total_periodo ?? 0);
            $sheet->getStyle('A9')->getNumberFormat()->setFormatCode('$#,##0.00');

            $sheet->setCellValue('B9', $kpi->transacciones ?? 0);
            $sheet->getStyle('B9')->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('C9', $kpi->ticket_promedio ?? 0);
            $sheet->mergeCells('C9:D9');
            $sheet->getStyle('C9')->getNumberFormat()->setFormatCode('$#,##0.00');

            $sheet->setCellValue('E9', $kpi->efectivo ?? 0);
            $sheet->mergeCells('E9:F9');
            $sheet->getStyle('E9')->getNumberFormat()->setFormatCode('$#,##0.00');

            $sheet->getStyle('A9:F9')->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 14, 'bold' => true, 'color' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A9')->applyFromArray($borderBlack);
            $sheet->getStyle('B9')->applyFromArray($borderBlack);
            $sheet->getStyle('C9:D9')->applyFromArray($borderBlack);
            $sheet->getStyle('E9:F9')->applyFromArray($borderBlack);
            $sheet->getRowDimension(9)->setRowHeight(26);

            // Fila 10: Vacía
            $sheet->getRowDimension(10)->setRowHeight(12);

            // Fila 11: Encabezados Tabla
            $headers = ['ID Venta', 'Fecha', 'Cliente', 'Método Pago', 'Estado', 'Total'];
            $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
            foreach ($headers as $i => $h) {
                $sheet->setCellValue($cols[$i] . '11', $h);
            }
            $sheet->getStyle('A11:F11')->applyFromArray($headerStyle);
            $sheet->getStyle('A11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension(11)->setRowHeight(26);

            // Datos
            $row = 12;
            foreach ($ventas as $v) {
                $sheet->setCellValue('A' . $row, '#' . str_pad($v->id, 5, '0', STR_PAD_LEFT));
                $sheet->setCellValue('B' . $row, $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—');
                $sheet->setCellValue('C' . $row, trim(($v->cliente?->nombre ?? 'Venta Mostrador') . ' ' . ($v->cliente?->apellido ?? '')));
                $sheet->setCellValue('D' . $row, $v->metodo_pago);
                $sheet->setCellValue('E' . $row, $v->estado);
                $sheet->setCellValue('F' . $row, $v->total);

                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('B' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('C' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('D' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('D' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('E' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setRGB($v->estado === 'Completada' ? '16A34A' : 'DC2626');

                $sheet->getStyle('F' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
            }

            if ($ventas->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'No se registraron ventas en este período.');
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
                $row++;
            }

            // Total Acumulado
            $sheet->setCellValue('A' . $row, 'TOTAL ACUMULADO:');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('F' . $row, $kpi->total_periodo ?? 0);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');

            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 12, 'bold' => true, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('F' . $row)->applyFromArray([
                'font' => ['name' => 'Times New Roman', 'size' => 14, 'bold' => true, 'color' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($borderBlack);
            $sheet->getStyle('F' . $row)->applyFromArray($borderBlack);
            $sheet->getRowDimension($row)->setRowHeight(26);

            // Anchos
            $sheet->getColumnDimension('A')->setWidth(16);
            $sheet->getColumnDimension('B')->setWidth(24);
            $sheet->getColumnDimension('C')->setWidth(28);
            $sheet->getColumnDimension('D')->setWidth(24);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(22);
        }

        // ==================== HOJA 2: PRODUCTOS MÁS VENDIDOS ====================
        if ($incluirProductos) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Prendas Mas Vendidas');
            $sheet->setShowGridLines(false);

            $sheet->getRowDimension(1)->setRowHeight(15);

            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A2');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(0);
                $drawing->setWorksheet($sheet);
            }

            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $run1 = $richText->createTextRun('Its ');
            $run1->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('000000');
            $run2 = $richText->createTextRun('Fashion');
            $run2->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->setCellValue('B2', $richText);
            $sheet->mergeCells('B2:D2');
            $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(36);

            $sheet->setCellValue('E2', 'Generado por: ' . $userName);
            $sheet->mergeCells('E2:F2');
            $sheet->getStyle('E2')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A3', 'Ranking de Prendas Más Vendidas');
            $sheet->mergeCells('A3:D3');
            $sheet->getStyle('A3')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('000000');
            $sheet->getRowDimension(3)->setRowHeight(24);

            $sheet->setCellValue('E3', 'Fecha: ' . $fechaGen);
            $sheet->mergeCells('E3:F3');
            $sheet->getStyle('E3')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A4', $rangoTexto);
            $sheet->mergeCells('A4:F4');
            $sheet->getStyle('A4')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getRowDimension(4)->setRowHeight(20);

            $sheet->getRowDimension(5)->setRowHeight(10);

            $sheet->setCellValue('A6', 'PRENDAS CON MAYOR ROTACIÓN');
            $sheet->mergeCells('A6:F6');
            $sheet->getStyle('A6')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->getStyle('A6:F6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)->getColor()->setRGB('2563EB');
            $sheet->getRowDimension(6)->setRowHeight(24);

            $sheet->getRowDimension(7)->setRowHeight(10);

            $sheet->setCellValue('A8', 'Posición');
            $sheet->setCellValue('B8', 'Producto / Prenda');
            $sheet->mergeCells('B8:C8');
            $sheet->setCellValue('D8', 'Talla');
            $sheet->setCellValue('E8', 'Color');
            $sheet->setCellValue('F8', 'Uds. Vendidas');

            $sheet->getStyle('A8:F8')->applyFromArray($headerStyle);
            $sheet->getStyle('A8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension(8)->setRowHeight(26);

            $row = 9;
            foreach ($productos as $idx => $p) {
                $sheet->setCellValue('A' . $row, $idx + 1);
                $sheet->setCellValue('B' . $row, $p->nombre);
                $sheet->mergeCells('B' . $row . ':C' . $row);
                $sheet->setCellValue('D' . $row, $p->talla);
                $sheet->setCellValue('E' . $row, $p->color);
                $sheet->setCellValue('F' . $row, $p->vendidos . ' uds');

                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('B' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('D' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('E' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('F' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true)->getColor()->setRGB('2563EB');
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
            }

            if ($productos->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'Sin datos de transacciones de prendas.');
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
            }

            $sheet->getColumnDimension('A')->setWidth(14);
            $sheet->getColumnDimension('B')->setWidth(24);
            $sheet->getColumnDimension('C')->setWidth(24);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(24);
        }

        // ==================== HOJA 3: INVENTARIO ====================
        if ($incluirInventario) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Inventario General');
            $sheet->setShowGridLines(false);

            $sheet->getRowDimension(1)->setRowHeight(15);

            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A2');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(0);
                $drawing->setWorksheet($sheet);
            }

            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $run1 = $richText->createTextRun('Its ');
            $run1->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('000000');
            $run2 = $richText->createTextRun('Fashion');
            $run2->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->setCellValue('B2', $richText);
            $sheet->mergeCells('B2:D2');
            $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(36);

            $sheet->setCellValue('E2', 'Generado por: ' . $userName);
            $sheet->mergeCells('E2:F2');
            $sheet->getStyle('E2')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A3', 'Reporte General de Inventario');
            $sheet->mergeCells('A3:D3');
            $sheet->getStyle('A3')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('000000');
            $sheet->getRowDimension(3)->setRowHeight(24);

            $sheet->setCellValue('E3', 'Fecha: ' . $fechaGen);
            $sheet->mergeCells('E3:F3');
            $sheet->getStyle('E3')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A4', 'Catálogo de prendas activas y stock disponible');
            $sheet->mergeCells('A4:F4');
            $sheet->getStyle('A4')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getRowDimension(4)->setRowHeight(20);

            $sheet->getRowDimension(5)->setRowHeight(10);

            $sheet->setCellValue('A6', 'ESTADO FÍSICO DEL STOCK');
            $sheet->mergeCells('A6:F6');
            $sheet->getStyle('A6')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->getStyle('A6:F6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)->getColor()->setRGB('2563EB');
            $sheet->getRowDimension(6)->setRowHeight(24);

            $sheet->getRowDimension(7)->setRowHeight(10);

            $sheet->setCellValue('A8', 'Producto');
            $sheet->mergeCells('A8:B8');
            $sheet->setCellValue('C8', 'Categoría');
            $sheet->setCellValue('D8', 'Talla');
            $sheet->setCellValue('E8', 'Color');
            $sheet->setCellValue('F8', 'Stock Disponible');

            $sheet->getStyle('A8:F8')->applyFromArray($headerStyle);
            $sheet->getStyle('D8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension(8)->setRowHeight(26);

            $row = 9;
            foreach ($inventario as $r) {
                $isCritical = ($r->stock == 0) || ($r->stock <= ($r->stock_minimo ?? 0));

                $sheet->setCellValue('A' . $row, $r->nombre);
                $sheet->mergeCells('A' . $row . ':B' . $row);
                $sheet->setCellValue('C' . $row, $r->categoria?->nombre ?? '—');
                $sheet->setCellValue('D' . $row, $r->talla);
                $sheet->setCellValue('E' . $row, $r->color);
                $sheet->setCellValue('F' . $row, $r->stock . ' uds');

                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('C' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('D' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('E' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('F' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                if ($isCritical) {
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('DC2626');
                }
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
            }

            if ($inventario->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'Catálogo de productos vacío.');
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
            }

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(24);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(24);
        }

        // ==================== HOJA 4: DEVOLUCIONES ====================
        if ($incluirDevoluciones) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Devoluciones');
            $sheet->setShowGridLines(false);

            $sheet->getRowDimension(1)->setRowHeight(15);

            if (file_exists($logoPath)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(48);
                $drawing->setCoordinates('A2');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(0);
                $drawing->setWorksheet($sheet);
            }

            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $run1 = $richText->createTextRun('Its ');
            $run1->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('000000');
            $run2 = $richText->createTextRun('Fashion');
            $run2->getFont()->setName('Times New Roman')->setSize(24)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->setCellValue('B2', $richText);
            $sheet->mergeCells('B2:D2');
            $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(36);

            $sheet->setCellValue('E2', 'Generado por: ' . $userName);
            $sheet->mergeCells('E2:F2');
            $sheet->getStyle('E2')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A3', 'Historial de Devoluciones y Reembolsos');
            $sheet->mergeCells('A3:D3');
            $sheet->getStyle('A3')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('000000');
            $sheet->getRowDimension(3)->setRowHeight(24);

            $sheet->setCellValue('E3', 'Fecha: ' . $fechaGen);
            $sheet->mergeCells('E3:F3');
            $sheet->getStyle('E3')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getStyle('E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A4', $rangoTexto);
            $sheet->mergeCells('A4:F4');
            $sheet->getStyle('A4')->getFont()->setName('Times New Roman')->setSize(11)->getColor()->setRGB('000000');
            $sheet->getRowDimension(4)->setRowHeight(20);

            $sheet->getRowDimension(5)->setRowHeight(10);

            $sheet->setCellValue('A6', 'DEVOLUCIONES SOLICITADAS');
            $sheet->mergeCells('A6:F6');
            $sheet->getStyle('A6')->getFont()->setName('Times New Roman')->setSize(14)->setBold(true)->getColor()->setRGB('2563EB');
            $sheet->getStyle('A6:F6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)->getColor()->setRGB('2563EB');
            $sheet->getRowDimension(6)->setRowHeight(24);

            $sheet->getRowDimension(7)->setRowHeight(10);

            $sheet->setCellValue('A8', 'ID Dev.');
            $sheet->setCellValue('B8', 'ID Venta');
            $sheet->setCellValue('C8', 'Fecha');
            $sheet->setCellValue('D8', 'Cliente / Motivo');
            $sheet->mergeCells('D8:E8');
            $sheet->setCellValue('F8', 'Monto Devuelto');

            $sheet->getStyle('A8:F8')->applyFromArray($headerStyle);
            $sheet->getStyle('A8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension(8)->setRowHeight(26);

            $row = 9;
            foreach ($devoluciones as $d) {
                $sheet->setCellValue('A' . $row, 'DEV-#' . str_pad($d->id, 3, '0', STR_PAD_LEFT));
                $sheet->setCellValue('B' . $row, '#' . str_pad($d->venta_id, 5, '0', STR_PAD_LEFT));
                $sheet->setCellValue('C' . $row, $d->fecha ? date('d/m/Y H:i', strtotime($d->fecha)) : '—');
                $sheet->setCellValue('D' . $row, trim(($d->cliente_nombre ?? '') . ' ' . ($d->cliente_apellido ?? '')) . ' - Motivo: "' . $d->motivo . '"');
                $sheet->mergeCells('D' . $row . ':E' . $row);
                $sheet->setCellValue('F' . $row, -$d->total_devolucion);

                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true)->getColor()->setRGB('DC2626');
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('B' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('C' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('C' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('D' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('D' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('F' . $row)->getFont()->setName('Times New Roman')->setSize(12)->setBold(true)->getColor()->setRGB('DC2626');
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('-$#,##0.00');
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;
            }

            if ($devoluciones->isEmpty()) {
                $sheet->setCellValue('A' . $row, 'No se registraron devoluciones.');
                $sheet->mergeCells('A' . $row . ':F' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setName('Times New Roman')->setSize(12);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($borderBlack);
            }

            $sheet->getColumnDimension('A')->setWidth(16);
            $sheet->getColumnDimension('B')->setWidth(16);
            $sheet->getColumnDimension('C')->setWidth(24);
            $sheet->getColumnDimension('D')->setWidth(28);
            $sheet->getColumnDimension('E')->setWidth(28);
            $sheet->getColumnDimension('F')->setWidth(24);
        }

        // Si no se creó ninguna hoja, crear una por defecto
        if ($spreadsheet->getSheetCount() === 0) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Reporte');
            $sheet->setCellValue('A1', 'Sin datos seleccionados.');
        }

        $spreadsheet->setActiveSheetIndex(0);

        // Generar archivo binario XLSX
        $fileName = 'Reporte_Its_Fashion_' . date('Ymd_Hi') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }
}
