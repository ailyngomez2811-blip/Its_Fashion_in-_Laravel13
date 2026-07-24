<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
        }

        /* Cabecera Principal */
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            height: 35px;
            vertical-align: bottom;
        }

        .brand-title span {
            color: #2563eb;
        }

        .main-subtitle {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            height: 30px;
            vertical-align: middle;
        }

        .info-text {
            font-size: 10px;
            color: #64748b;
            height: 20px;
            vertical-align: top;
        }

        /* KPIs Estilo Premium en Excel */
        .kpi-cell {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
            height: 40px;
        }

        .kpi-val {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .kpi-lbl {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }

        /* Títulos de Sección */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            height: 35px;
            vertical-align: bottom;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 4px;
            text-transform: uppercase;
        }

        /* Cabeceras de Tabla */
        .th-header {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            height: 28px;
            text-align: left;
            padding-left: 8px;
            text-transform: uppercase;
            font-size: 9px;
        }

        /* Celdas de Datos */
        .td-data {
            border: 1px solid #e2e8f0;
            height: 26px;
            color: #334155;
            padding-left: 8px;
            vertical-align: middle;
        }

        .tr-even td {
            background-color: #f8fafc;
        }

        .td-bold {
            font-weight: bold;
            color: #0f172a;
        }

        .td-red {
            color: #b91c1c;
            font-weight: bold;
        }

        .td-green {
            color: #16a34a;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
            padding-right: 8px;
        }

        .text-center {
            text-align: center;
        }
    </style>
    @verbatim
    <!--[if gte mso 9]>
    <xml>
     <x:ExcelWorkbook>
      <x:ExcelWorksheets>
       <x:ExcelWorksheet>
        <x:Name>Reporte Its Fashion</x:Name>
        <x:WorksheetOptions>
         <x:Selected />
        </x:WorksheetOptions>
       </x:ExcelWorksheet>
      </x:ExcelWorksheets>
     </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
</head>
<body>

    <!-- UNA SOLA TABLA UNIFICADA PARA ALINEACIÓN PERFECTA -->
    <table width="800" style="table-layout: fixed;">
        <!-- Definiendo anchos de columna estrictos para todo el reporte -->
        <col width="110">
        <col width="150">
        <col width="200">
        <col width="110">
        <col width="110">
        <col width="120">

        <!-- CABECERA -->
        <tr>
            <td colspan="4" class="brand-title">Its <span>Fashion</span></td>
            <td colspan="2" style="text-align: right; font-size: 9px; color: #64748b; vertical-align: bottom;">
                <strong>Generado por:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="main-subtitle">Consolidado de Actividad Comercial</td>
            <td colspan="2" style="text-align: right; font-size: 9px; color: #64748b; vertical-align: middle;">
                <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="info-text">
                Rango de evaluación: <strong>{{ date('d/m/Y', strtotime($desde)) }}</strong> al <strong>{{ date('d/m/Y', strtotime($hasta)) }}</strong>
            </td>
        </tr>
        <tr><td colspan="6" style="height: 10px;"></td></tr>

        <!-- VENTAS E INGRESOS -->
        @if ($incluirVentas)
            <tr>
                <td colspan="6" class="section-title">Ventas e Ingresos</td>
            </tr>
            <tr><td colspan="6" style="height: 5px;"></td></tr>
            
            <!-- KPIs -->
            <tr>
                <td class="kpi-cell">
                    <span class="kpi-val">${{ number_format($kpi->total_periodo ?? 0, 2) }}</span><br>
                    <span class="kpi-lbl">Total Ingresos</span>
                </td>
                <td class="kpi-cell">
                    <span class="kpi-val">{{ $kpi->transacciones ?? 0 }}</span><br>
                    <span class="kpi-lbl">Ventas</span>
                </td>
                <td class="kpi-cell" colspan="2">
                    <span class="kpi-val">${{ number_format($kpi->ticket_promedio ?? 0, 2) }}</span><br>
                    <span class="kpi-lbl">Ticket Promedio</span>
                </td>
                <td class="kpi-cell" colspan="2">
                    <span class="kpi-val">${{ number_format($kpi->efectivo ?? 0, 2) }}</span><br>
                    <span class="kpi-lbl">Efectivo</span>
                </td>
            </tr>
            <tr><td colspan="6" style="height: 10px;"></td></tr>

            <!-- Encabezados de tabla -->
            <tr>
                <td class="th-header">ID Venta</td>
                <td class="th-header">Fecha</td>
                <td class="th-header">Cliente</td>
                <td class="th-header">Método Pago</td>
                <td class="th-header">Estado</td>
                <td class="th-header text-right">Total</td>
            </tr>
            @forelse ($ventas as $idx => $v)
                <tr class="{{ $idx % 2 === 0 ? '' : 'tr-even' }}">
                    <td class="td-data font-mono" style="font-weight: bold; color: #475569;">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="td-data">{{ $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—' }}</td>
                    <td class="td-data td-bold">{{ $v->cliente->nombre ?? 'Venta Mostrador' }} {{ $v->cliente->apellido ?? '' }}</td>
                    <td class="td-data">{{ $v->metodo_pago }}</td>
                    <td class="td-data text-center"><span class="badge-txt {{ $v->estado === 'Completada' ? 'badge-success' : 'badge-danger' }}">{{ $v->estado }}</span></td>
                    <td class="td-data td-bold text-right">${{ number_format($v->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="td-data text-center" style="color: #94a3b8;">No se registraron ventas en este período.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="5" class="td-data td-bold text-right" style="background-color: #f8fafc; font-weight: bold;">TOTAL ACUMULADO:</td>
                <td class="td-data td-bold text-right text-right" style="background-color: #f8fafc; color: #2563eb; font-weight: bold;">
                    ${{ number_format($kpi->total_periodo ?? 0, 2) }}
                </td>
            </tr>
            <tr><td colspan="6" style="height: 25px;"></td></tr>
        @endif

        <!-- PRODUCTOS MÁS VENDIDOS -->
        @if ($incluirProductos)
            <tr>
                <td colspan="6" class="section-title">Ranking de Prendas Más Vendidas</td>
            </tr>
            <tr><td colspan="6" style="height: 5px;"></td></tr>
            <tr>
                <td class="th-header text-center">Posición</td>
                <td class="th-header" colspan="2">Producto / Prenda</td>
                <td class="th-header">Talla</td>
                <td class="th-header">Color</td>
                <td class="th-header text-right">Uds. Vendidas</td>
            </tr>
            @forelse ($productos as $idx => $p)
                <tr class="{{ $idx % 2 === 0 ? '' : 'tr-even' }}">
                    <td class="td-data text-center" style="font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="td-data td-bold" colspan="2" style="color: #0f172a;">{{ $p->nombre }}</td>
                    <td class="td-data">{{ $p->talla }}</td>
                    <td class="td-data">{{ $p->color }}</td>
                    <td class="td-data td-bold text-right" style="color: #1e3a8a;">{{ $p->vendidos }} uds</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="td-data text-center" style="color: #94a3b8;">Sin datos de transacciones de prendas.</td>
                </tr>
            @endforelse
            <tr><td colspan="6" style="height: 25px;"></td></tr>
        @endif

        <!-- INVENTARIO COMPLETO -->
        @if ($incluirInventario)
            <tr>
                <td colspan="6" class="section-title">Reporte General de Inventario</td>
            </tr>
            <tr><td colspan="6" style="height: 5px;"></td></tr>
            <tr>
                <td class="th-header" colspan="2">Producto</td>
                <td class="th-header">Categoría</td>
                <td class="th-header">Talla</td>
                <td class="th-header">Color</td>
                <td class="th-header text-right">Stock Disponible</td>
            </tr>
            @forelse ($inventario as $idx => $r)
                @php
                    $isCritical = ($r->stock == 0) || ($r->stock <= ($r->stock_minimo ?? 0));
                @endphp
                <tr class="{{ $idx % 2 === 0 ? '' : 'tr-even' }}">
                    <td class="td-data td-bold" colspan="2">{{ $r->nombre }}</td>
                    <td class="td-data">{{ $r->categoria->nombre ?? '—' }}</td>
                    <td class="td-data">{{ $r->talla }}</td>
                    <td class="td-data">{{ $r->color }}</td>
                    <td class="td-data text-right {{ $isCritical ? 'td-red' : 'td-bold' }}">{{ $r->stock }} uds</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="td-data text-center" style="color: #94a3b8;">Catálogo de productos vacío.</td>
                </tr>
            @endforelse
            <tr><td colspan="6" style="height: 25px;"></td></tr>
        @endif

        <!-- DEVOLUCIONES -->
        @if ($incluirDevoluciones)
            <tr>
                <td colspan="6" class="section-title">Devoluciones y Reembolsos</td>
            </tr>
            <tr><td colspan="6" style="height: 5px;"></td></tr>
            <tr>
                <td class="th-header">ID Dev.</td>
                <td class="th-header">ID Venta</td>
                <td class="th-header">Fecha</td>
                <td class="th-header" colspan="2">Cliente / Motivo</td>
                <td class="th-header text-right">Monto Devuelto</td>
            </tr>
            @forelse ($devoluciones as $idx => $d)
                <tr class="{{ $idx % 2 === 0 ? '' : 'tr-even' }}">
                    <td class="td-data td-red font-mono" style="font-weight: bold;">DEV-#{{ str_pad($d->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="td-data font-mono">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="td-data">{{ $d->fecha ? $d->fecha->format('d/m/Y H:i') : '—' }}</td>
                    <td class="td-data" colspan="2">
                        <span class="td-bold">{{ $d->cliente_nombre }} {{ $d->cliente_apellido }}</span><br>
                        <span style="color: #64748b; font-size: 10px; font-style: italic;">Motivo: "{{ $d->motivo }}"</span>
                    </td>
                    <td class="td-data td-red text-right">-${{ number_format($d->total_devolucion, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="td-data text-center" style="color: #94a3b8;">No se registraron devoluciones.</td>
                </tr>
            @endforelse
        @endif
    </table>

</body>
</html>
