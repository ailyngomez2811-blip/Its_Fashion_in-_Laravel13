<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Actividad - Its Fashion</title>
    <style>
        @page {
            margin: 80px 45px 60px 45px;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.5;
        }

        /* Cabecera Repetitiva */
        header {
            position: fixed;
            top: -55px;
            left: 0px;
            right: 0px;
            height: 45px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }
        
        .header-logo {
            float: left;
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        
        .header-logo span {
            color: #2563eb;
        }
        
        .header-meta {
            float: right;
            text-align: right;
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Pie de página */
        footer {
            position: fixed;
            bottom: -35px;
            left: 0px;
            right: 0px;
            height: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .page-number:after {
            content: counter(page);
        }

        /* Contenido Principal */
        .content {
            margin-top: 10px;
        }

        .title-block {
            margin-bottom: 20px;
        }

        .title-block h1 {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .title-block p {
            font-size: 11px;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        /* KPIs Estilo Premium */
        .kpi-container {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-left: -10px;
            margin-right: -10px;
        }
        
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px;
            text-align: center;
            border-radius: 10px;
        }
        
        .kpi-val {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
        }
        
        .kpi-lbl {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Títulos de Sección */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 25px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            page-break-after: avoid;
        }

        /* Tablas */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table.data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 9px 12px;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table.data-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        table.data-table tr:nth-child(even) td {
            background-color: #fdfdfd;
        }

        /* Badges de Estado */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #075985; }

        .text-right {
            text-align: right;
        }
        
        .font-mono {
            font-family: monospace;
        }

        .page-break {
            page-break-after: always;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <header class="clearfix">
        <div class="header-logo">Its <span>Fashion</span></div>
        <div class="header-meta">
            <strong>Generado por:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}<br>
            <strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
    </header>

    <!-- PIE DE PÁGINA -->
    <footer>
        Its Fashion Store - Reporte de Operaciones · Página <span class="page-number"></span>
    </footer>

    <!-- CONTENIDO -->
    <div class="content">
        
        <!-- Bloque del Título de Portada -->
        <div class="title-block">
            <h1>Consolidado de Actividad Comercial</h1>
            <p>Rango de evaluación: <strong>{{ date('d/m/Y', strtotime($desde)) }}</strong> al <strong>{{ date('d/m/Y', strtotime($hasta)) }}</strong></p>
        </div>

        <!-- SECCIÓN 1: VENTAS E INGRESOS -->
        @if ($incluirVentas)
            <div class="section-title">Ventas e Ingresos</div>
            
            <table class="kpi-container">
                <tr>
                    <td style="width: 25%;">
                        <div class="kpi-card">
                            <div class="kpi-val">${{ number_format($kpi->total_periodo ?? 0, 2) }}</div>
                            <div class="kpi-lbl">Total Ingresos</div>
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div class="kpi-card">
                            <div class="kpi-val">{{ $kpi->transacciones ?? 0 }}</div>
                            <div class="kpi-lbl">Ventas</div>
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div class="kpi-card">
                            <div class="kpi-val">${{ number_format($kpi->ticket_promedio ?? 0, 2) }}</div>
                            <div class="kpi-lbl">Ticket Prom.</div>
                        </div>
                    </td>
                    <td style="width: 25%;">
                        <div class="kpi-card">
                            <div class="kpi-val">${{ number_format($kpi->efectivo ?? 0, 2) }}</div>
                            <div class="kpi-lbl">Efectivo</div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">ID Venta</th>
                        <th style="width: 20%;">Fecha</th>
                        <th>Cliente</th>
                        <th style="width: 18%;">Método Pago</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 15%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $v)
                        <tr>
                            <td class="font-mono" style="font-weight: bold; color: #475569;">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td style="font-weight: 500;">{{ $v->cliente->nombre ?? 'Venta Mostrador' }} {{ $v->cliente->apellido ?? '' }}</td>
                            <td>{{ $v->metodo_pago }}</td>
                            <td><span class="badge {{ $v->estado === 'Completada' ? 'badge-success' : 'badge-danger' }}">{{ $v->estado }}</span></td>
                            <td class="text-right" style="font-weight: bold; color: #0f172a;">${{ number_format($v->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">No se registraron ventas en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- SECCIÓN 2: PRODUCTOS MÁS VENDIDOS -->
        @if ($incluirProductos)
            @if ($incluirVentas) <div class="page-break"></div> @endif
            <div class="section-title">Ranking de Prendas Más Vendidas</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">Pos</th>
                        <th>Producto / Prenda</th>
                        <th style="width: 15%;">Talla</th>
                        <th style="width: 15%;">Color</th>
                        <th style="width: 20%; text-align: center;">Uds. Vendidas</th>
                        <th style="width: 20%; text-align: right;">Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $i => $p)
                        <tr>
                            <td style="font-weight: bold; text-align: center; color: #64748b;">{{ $i + 1 }}</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $p->nombre }}</td>
                            <td>{{ $p->talla }}</td>
                            <td>{{ $p->color }}</td>
                            <td style="text-align: center; font-weight: bold; color: #1e3a8a;">{{ $p->vendidos }} uds</td>
                            <td class="text-right" style="font-weight: bold; color: #16a34a;">${{ number_format($p->ingresos, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">Sin transacciones de prendas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- SECCIÓN 3: INVENTARIO ACTUAL -->
        @if ($incluirInventario)
            @if ($incluirVentas || $incluirProductos) <div class="page-break"></div> @endif
            <div class="section-title">Reporte General de Inventario</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th style="width: 12%;">Talla</th>
                        <th style="width: 12%;">Color</th>
                        <th style="width: 15%; text-align: center;">Stock</th>
                        <th style="width: 15%; text-align: center;">Mínimo</th>
                        <th style="width: 18%;">Alerta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventario as $r)
                        @php
                            $status = 'Disponible';
                            if ($r->stock == 0) {
                                $status = 'Agotado';
                            } elseif ($r->stock <= ($r->stock_minimo ?? 0)) {
                                $status = 'Crítico';
                            }
                            $bc = $status === 'Disponible' ? 'badge-success' : ($status === 'Crítico' ? 'badge-warning' : 'badge-danger');
                        @endphp
                        <tr>
                            <td style="font-weight: bold; color: #0f172a;">{{ $r->nombre }}</td>
                            <td>{{ $r->categoria->nombre ?? '—' }}</td>
                            <td>{{ $r->talla }}</td>
                            <td>{{ $r->color }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $r->stock }}</td>
                            <td style="text-align: center; color: #64748b;">{{ $r->stock_minimo ?? 0 }}</td>
                            <td><span class="badge {{ $bc }}">{{ $status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">Catálogo de productos vacío.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- SECCIÓN 4: DEVOLUCIONES -->
        @if ($incluirDevoluciones)
            @if ($incluirVentas || $incluirProductos || $incluirInventario) <div class="page-break"></div> @endif
            <div class="section-title">Historial de Devoluciones</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">ID Dev.</th>
                        <th style="width: 15%;">ID Venta</th>
                        <th style="width: 18%;">Fecha</th>
                        <th>Cliente</th>
                        <th>Motivo</th>
                        <th style="width: 18%; text-align: right;">Total Devuelto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($devoluciones as $d)
                        <tr>
                            <td class="font-mono" style="font-weight: bold; color: #b91c1c;">DEV-#{{ str_pad($d->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="font-mono">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $d->fecha ? $d->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td style="font-weight: 500;">{{ $d->cliente_nombre }} {{ $d->cliente_apellido }}</td>
                            <td style="color: #475569;"><em>"{{ $d->motivo }}"</em></td>
                            <td class="text-right" style="font-weight: bold; color: #b91c1c;">-${{ number_format($d->total_devolucion, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">No se registraron devoluciones en este período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

    </div>

</body>
</html>
