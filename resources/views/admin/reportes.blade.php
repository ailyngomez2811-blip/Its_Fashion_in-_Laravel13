@extends('layouts.sidebaradmin')

@section('titulo', 'Reportes y Estadísticas')

@push('estilos')
<style>
    .trow:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-card {
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        background: white;
    }

    .tab-btn {
        padding: 12px 20px;
        font-weight: 600;
        color: #64748b;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        color: #2563eb;
    }

    .tab-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    .modal-box {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 520px;
    }

    @keyframes scaleUp {
        from {
            opacity: 0;
            transform: scale(.97)
        }
        to {
            opacity: 1;
            transform: scale(1)
        }
    }

    .scale-up {
        animation: scaleUp .3s ease-out;
    }
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado y Filtros rápidos -->
    <div class="flex flex-col gap-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
                <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Reportes y Estadísticas</h1>
            </div>
            
            <button onclick="openExportar()" 
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-file-export text-xs"></i>Exportar Reporte
            </button>
        </div>

        <!-- Filtros de fecha -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
                @foreach (['Esta semana', 'Este mes', 'Últimos 3 meses', 'Este año'] as $p)
                    <button onclick="setPeriodo('{{ $p }}')" 
                        class="px-4 py-2 text-xs font-semibold rounded-xl border transition-all whitespace-nowrap {{ $periodo === $p ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        {{ $p }}
                    </button>
                @endforeach
            </div>

            <!-- Selector personalizado -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <div class="flex items-center gap-1.5 bg-slate-50 border-2 border-slate-200 px-3 py-1.5 rounded-xl">
                    <span class="text-xs text-slate-400 font-bold uppercase">Desde</span>
                    <input type="date" id="input-desde" value="{{ $desde }}" 
                        class="bg-transparent text-sm text-slate-700 font-medium focus:outline-none cursor-pointer">
                </div>
                <div class="flex items-center gap-1.5 bg-slate-50 border-2 border-slate-200 px-3 py-1.5 rounded-xl">
                    <span class="text-xs text-slate-400 font-bold uppercase">Hasta</span>
                    <input type="date" id="input-hasta" value="{{ $hasta }}" 
                        class="bg-transparent text-sm text-slate-700 font-medium focus:outline-none cursor-pointer">
                </div>
                <button onclick="aplicarRango()" 
                    class="px-4 py-2.5 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-700 transition">
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- KPIs del Período -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($kpi->total_periodo ?? 0, 2) }}</p>
                    <p class="text-xs text-slate-500">Ingresos Totales</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $kpi->transacciones ?? 0 }}</p>
                    <p class="text-xs text-slate-500">Ventas Realizadas</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 text-purple-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($kpi->ticket_promedio ?? 0, 2) }}</p>
                    <p class="text-xs text-slate-500">Ticket Promedio</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $total_clientes }}</p>
                    <p class="text-xs text-slate-500">Clientes Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas de Reportes -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm mb-6" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <button onclick="switchTab('ventas', this)" class="tab-btn active"><i class="fas fa-chart-line mr-1.5"></i>Ventas e Ingresos</button>
            <button onclick="switchTab('compras', this)" class="tab-btn"><i class="fas fa-truck mr-1.5"></i>Compras (Abastecimiento)</button>
            <button onclick="switchTab('inventario', this)" class="tab-btn"><i class="fas fa-warehouse mr-1.5"></i>Inventario Actual</button>
            <button onclick="switchTab('productos', this)" class="tab-btn"><i class="fas fa-tshirt mr-1.5"></i>Productos Más Vendidos</button>
            <button onclick="switchTab('devoluciones', this)" class="tab-btn"><i class="fas fa-undo-alt mr-1.5"></i>Devoluciones</button>
        </div>

        <div class="p-6">
            <!-- PESTAÑA 1: VENTAS E INGRESOS -->
            <div id="tab-ventas" class="tab-content active space-y-6">
                <!-- Gráfico Principal de Ventas -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <h4 class="font-bold text-slate-700 text-sm mb-4">{{ $label_grafica }} ({{ $periodo }})</h4>
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <!-- Métodos de pago -->
                    <div class="lg:col-span-2 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                        <h4 class="font-bold text-slate-700 text-sm mb-4">Métodos de Pago</h4>
                        @php
                            $ef = (float)($kpi->efectivo ?? 0);
                            $tr = (float)($kpi->transferencia ?? 0);
                            $tot = $ef + $tr;
                            $pct_ef = $tot > 0 ? round(($ef / $tot) * 100) : 0;
                            $pct_tr = $tot > 0 ? round(($tr / $tot) * 100) : 0;
                        @endphp
                        @if ($tot === 0.0)
                            <div class="py-12 text-center text-slate-400">
                                <i class="fas fa-credit-card text-3xl mb-2 block opacity-20"></i>
                                <p class="text-xs">Sin transacciones registradas</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm font-semibold text-slate-700">Efectivo</span>
                                    </div>
                                    <span class="font-bold text-blue-600">{{ $pct_ef }}% (${{ number_format($ef, 2) }})</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm font-semibold text-slate-700">Transferencia</span>
                                    </div>
                                    <span class="font-bold text-purple-600">{{ $pct_tr }}% (${{ number_format($tr, 2) }})</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tendencia de Ventas (6 meses) -->
                    <div class="lg:col-span-3 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                        <h4 class="font-bold text-slate-700 text-sm mb-4">Tendencia de Ventas (Últimos 6 meses)</h4>
                        @if (array_sum($ventas_mes) === 0)
                            <div class="py-12 text-center text-slate-400">
                                <i class="fas fa-chart-bar text-3xl mb-2 block opacity-20"></i>
                                <p class="text-xs">Sin historial de ventas</p>
                            </div>
                        @else
                            <div class="flex items-end gap-3 h-48 pt-4">
                                @php
                                    $max_m = max($ventas_mes) ?: 1;
                                @endphp
                                @foreach ($ventas_mes as $i => $v)
                                    @php
                                        $h = round(($v / $max_m) * 100);
                                    @endphp
                                    <div class="flex-1 flex flex-col justify-end items-center gap-1 h-full">
                                        <span class="text-[10px] text-slate-500 font-bold">${{ number_format($v, 0) }}</span>
                                        <div class="w-full rounded-t-lg transition-all" 
                                            style="height:{{ max($h, 8) }}%; background: {{ $i === count($ventas_mes)-1 ? 'linear-gradient(135deg, #2563eb, #0ea5e9)' : '#bfdbfe' }}"></div>
                                        <span class="text-[10px] text-slate-400 font-semibold uppercase mt-1">{{ $meses[$i] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PESTAÑA 2: INVENTARIO ACTUAL -->
            <div id="tab-inventario" class="tab-content">
                @if ($inv->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <i class="fas fa-warehouse text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">No hay productos registrados en el inventario.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr>
                                    @foreach (['Producto', 'Categoría', 'Talla / Color', 'Stock actual', 'Stock Mínimo', 'Estado'] as $h)
                                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inv as $r)
                                    @php
                                        $status = 'Disponible';
                                        if ($r->stock == 0) {
                                            $status = 'Agotado';
                                        } elseif ($r->stock <= ($r->stock_minimo ?? 0)) {
                                            $status = 'Crítico';
                                        }
                                        $bc = $status === 'Disponible' ? 'bg-emerald-100 text-emerald-700' : ($status === 'Crítico' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                    @endphp
                                    <tr class="trow text-slate-700 border-b border-slate-50">
                                        <td class="px-5 py-4 font-bold text-slate-800 text-sm">{{ $r->nombre }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ $r->categoria->nombre ?? '—' }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ $r->talla }} <span class="text-slate-400">/</span> {{ $r->color }}</td>
                                        <td class="px-5 py-4 text-sm font-bold text-slate-800">{{ $r->stock }} uds</td>
                                        <td class="px-5 py-4 text-sm text-slate-500">{{ $r->stock_minimo ?? 0 }} uds</td>
                                        <td class="px-5 py-4"><span class="badge {{ $bc }}">{{ $status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- PESTAÑA 3: PRODUCTOS MÁS VENDIDOS -->
            <div id="tab-productos" class="tab-content">
                @if ($top_productos->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <i class="fas fa-tshirt text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">Sin datos de ventas en este período.</p>
                    </div>
                @else
                    @php
                        $max_v = $top_productos->max('vendidos') ?: 1;
                    @endphp
                    <div class="space-y-5">
                        @foreach ($top_productos as $i => $p)
                            @php
                                $pct = round(($p->vendidos / $max_v) * 100);
                                $color = match($i) {
                                    0 => 'bg-blue-600',
                                    1 => 'bg-emerald-500',
                                    2 => 'bg-purple-500',
                                    3 => 'bg-amber-500',
                                    default => 'bg-pink-500',
                                };
                            @endphp
                            <div class="flex items-center gap-4">
                                <span class="w-6 text-center text-sm font-bold text-slate-400">{{ $i + 1 }}</span>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div>
                                            <span class="font-bold text-slate-800 text-sm">{{ $p->nombre }}</span>
                                            <span class="text-xs text-slate-400 ml-1.5">{{ $p->talla }} / {{ $p->color }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-bold text-slate-800">{{ $p->vendidos }} uds</span>
                                            <span class="text-xs text-slate-500 block">${{ number_format($p->ingresos, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $color }}" style="width: {{ $pct }}%; transition: width 0.6s;"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- PESTAÑA DE COMPRAS (ABASTECIMIENTO) -->
            <div id="tab-compras" class="tab-content space-y-6">
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-700">Inversión Total en Abastecimiento</span>
                    <span class="text-xl font-bold text-indigo-600">${{ number_format($totalInversionCompras, 2) }}</span>
                </div>
                @if ($comprasPeriodo->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <i class="fas fa-truck text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">No se registran compras/abastecimiento en este período.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr>
                                    @foreach (['# Compra', 'Fecha', 'Proveedor', 'Registrado Por', 'Monto Invertido'] as $h)
                                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comprasPeriodo as $c)
                                    <tr class="trow text-slate-700 border-b border-slate-50">
                                        <td class="px-5 py-4 font-mono text-sm text-slate-800">#{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-5 py-4 text-sm">{{ $c->fecha ? $c->fecha->format('d/m/Y H:i') : '—' }}</td>
                                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $c->proveedor->nombre ?? '—' }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ $c->usuario->nombre ?? '—' }} {{ $c->usuario->apellido ?? '' }}</td>
                                        <td class="px-5 py-4 text-sm font-bold text-slate-800">${{ number_format($c->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- PESTAÑA 4: DEVOLUCIONES -->
            <div id="tab-devoluciones" class="tab-content">
                @if ($devols->isEmpty())
                    <div class="py-16 text-center text-slate-400">
                        <i class="fas fa-undo-alt text-3xl mb-3 block opacity-20"></i>
                        <p class="text-sm">No se registran devoluciones en este período.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr>
                                    @foreach (['Fecha', 'ID Venta', 'Cliente', 'Motivo', 'Monto devuelto'] as $h)
                                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($devols as $d)
                                    <tr class="trow text-slate-700 border-b border-slate-50">
                                        <td class="px-5 py-4 text-sm">{{ $d->fecha ? $d->fecha->format('d/m/Y H:i') : '' }}</td>
                                        <td class="px-5 py-4 font-mono text-xs text-slate-500">#{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $d->cliente_nombre }} {{ $d->cliente_apellido }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600"><em>"{{ $d->motivo }}"</em></td>
                                        <td class="px-5 py-4 text-sm font-bold text-red-600">${{ number_format($d->total_devolucion, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- MODAL EXPORTAR REPORTE -->
<div id="modal-exp" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Exportar Reportes</h3>
            <button onclick="closeExportar()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <!-- Seleccionar secciones -->
            <div>
                <p class="text-sm font-semibold text-slate-700 mb-3">¿Qué deseas incluir en el reporte?</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach (['Ventas', 'Inventario', 'Productos más vendidos', 'Devoluciones'] as $r)
                        @php
                            $val = str_replace(' ', '_', $r);
                        @endphp
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-100 hover:border-blue-300 cursor-pointer transition bg-slate-50 hover:bg-white select-none">
                            <input type="checkbox" value="{{ $val }}" class="accent-blue-600 report-cb" checked>
                            <span class="text-sm font-medium text-slate-700">{{ $r }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Formato de descarga -->
            <div class="pt-2">
                <p class="text-sm font-semibold text-slate-700 mb-3">Elige el formato de descarga</p>
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="exportar('PDF')" class="flex items-center justify-center gap-2 py-3 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 font-bold text-sm transition">
                        <i class="fas fa-file-pdf"></i><span>PDF Documento</span>
                    </button>
                    <button onclick="exportar('Excel')" class="flex items-center justify-center gap-2 py-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-sm transition">
                        <i class="fas fa-file-excel"></i><span>Excel Planilla</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ── Configuración de Pestañas (Tabs) ───────────────────────────────────────
    function switchTab(tab, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
    }

    // Rango de fechas rápido
    function setPeriodo(p) {
        window.location.href = "{{ route('reportes.index') }}?periodo=" + encodeURIComponent(p);
    }

    // Rango personalizado
    function aplicarRango() {
        const desde = document.getElementById('input-desde').value;
        const hasta = document.getElementById('input-hasta').value;
        if (!desde || !hasta) {
            Swal.fire({ icon: 'warning', title: 'Advertencia', text: 'Selecciona ambas fechas para aplicar el rango personalizado.' });
            return;
        }
        window.location.href = "{{ route('reportes.index') }}?periodo=Personalizado&desde=" + desde + "&hasta=" + hasta;
    }

    // ── Control de Modales de Exportar ──────────────────────────────────────────
    function openExportar() {
        document.getElementById('modal-exp').classList.remove('hidden');
    }

    function closeExportar() {
        document.getElementById('modal-exp').classList.add('hidden');
    }

    document.getElementById('modal-exp').addEventListener('click', e => {
        if (e.target === document.getElementById('modal-exp')) closeExportar();
    });

    function exportar(fmt) {
        const checkboxes = document.querySelectorAll('.report-cb:checked');
        const params = [];
        checkboxes.forEach(cb => {
            params.push(encodeURIComponent(cb.value) + '=1');
        });

        const desde = document.getElementById('input-desde').value;
        const hasta = document.getElementById('input-hasta').value;
        if (desde) params.push('desde=' + encodeURIComponent(desde));
        if (hasta) params.push('hasta=' + encodeURIComponent(hasta));

        const queryString = params.length > 0 ? '?' + params.join('&') : '';
        
        if (fmt === 'PDF') {
            window.open("{{ route('reportes.exportar.pdf') }}" + queryString, '_blank');
            showToast('Generando reporte PDF...');
        } else if (fmt === 'Excel') {
            window.open("{{ route('reportes.exportar.excel') }}" + queryString, '_blank');
            showToast('Descargando planilla Excel...');
        }
        closeExportar();
    }

    function showToast(msg, type = 'success') {
        Swal.fire({
            icon: type,
            title: msg,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // ── RENDERIZADO DE CHART.JS (VENTAS POR PERÍODO) ───────────────────────────
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = {!! json_encode($dias) !!};
        const data = {!! json_encode($ventas_semana) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#ffffff',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Outfit', size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Outfit', size: 11 },
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
