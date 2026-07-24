@extends('layouts.sidebaradmin')

@section('titulo', 'Inventario y Kardex')

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
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-warehouse text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Control de Inventario y Kardex</h1>
        </div>
    </div>

    <!-- KPIs de Existencias -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/25">
                    <i class="fas fa-box text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['total'] }}</p>
                    <p class="text-xs text-slate-500">Total Productos</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['conStock'] }}</p>
                    <p class="text-xs text-slate-500">Con Stock</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['critico'] }}</p>
                    <p class="text-xs text-slate-500">Stock Crítico</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-red-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-times-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['sinStock'] }}</p>
                    <p class="text-xs text-slate-500">Sin Stock</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: TABLA DE INVENTARIO Y STOCK ACTUAL -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm mb-6" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="p-6 border-b border-slate-100 flex flex-col gap-4">
            <div class="flex justify-between items-center flex-wrap gap-2">
                <div>
                    <h3 class="font-bold text-slate-800">Estado del Stock</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Control de almacén y precios de venta</p>
                </div>
            </div>

            <!-- Filtros de Productos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <div class="relative md:col-span-2">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="inv-search" placeholder="Buscar por nombre..." oninput="filtrarInventario()"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-brand-accent transition">
                </div>
                <div>
                    <select id="inv-categoria" onchange="filtrarInventario()"
                        class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ strtolower($cat->nombre) }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="inv-estado" onchange="filtrarInventario()"
                        class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                        <option value="todos">Todas las existencias</option>
                        <option value="con_stock">Con stock</option>
                        <option value="critico">Stock crítico</option>
                        <option value="sin_stock">Sin stock</option>
                    </select>
                </div>
                <div>
                    <button onclick="limpiarFiltros()" 
                        class="w-full px-3 py-2.5 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all flex items-center justify-center gap-1.5">
                        <i class="fas fa-times text-xs"></i><span>Limpiar</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Producto', 'Categoría', 'Talla / Color', 'P. Compra', 'P. Venta', 'Stock Actual', 'Stock Mín.', 'Estado'] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="inv-table-body">
                    @forelse ($productos as $p)
                        @php
                            $stockStatus = 'con_stock';
                            if ($p->stock == 0) {
                                $stockStatus = 'sin_stock';
                            } elseif ($p->stock <= ($p->stock_minimo ?? 0)) {
                                $stockStatus = 'critico';
                            }

                            $badgeStock = match($stockStatus) {
                                'sin_stock' => 'bg-red-100 text-red-700',
                                'critico'   => 'bg-amber-100 text-amber-700',
                                default     => 'bg-emerald-100 text-emerald-700',
                            };
                            $iconStock = match($stockStatus) {
                                'sin_stock' => 'fa-times-circle',
                                'critico'   => 'fa-exclamation-triangle',
                                default     => 'fa-check-circle',
                            };
                            $dataSearch = strtolower("{$p->nombre} {$p->talla} {$p->color} " . ($p->categoria->nombre ?? ''));
                        @endphp
                        <tr class="trow-inv border-b border-slate-50 hover:bg-slate-50 text-slate-700"
                            data-search="{{ $dataSearch }}"
                            data-categoria="{{ strtolower($p->categoria->nombre ?? '') }}"
                            data-stock-status="{{ $stockStatus }}">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 text-sm">{{ $p->nombre }}</p>
                                <p class="text-xs text-slate-400">ID: #{{ $p->id }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $p->categoria->nombre ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                <span class="font-semibold">{{ $p->talla }}</span> <span class="text-slate-400 mx-0.5">/</span> {{ $p->color }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">${{ number_format($p->precio_compra, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-800">${{ number_format($p->precio_venta, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $badgeStock }}">
                                    <i class="fas {{ $iconStock }}"></i> {{ $p->stock }} uds
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $p->stock_minimo ?? 0 }} uds</td>
                            <td class="px-6 py-4">
                                <span class="badge text-xs font-semibold px-2.5 py-1 rounded-full {{ $p->estado === 'Activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $p->estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-slate-400 text-sm">
                                <i class="fas fa-box-open text-4xl mb-3 block opacity-20"></i>
                                <p>No hay productos registrados en el catálogo.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="inv-empty" class="hidden py-14 text-center text-slate-400">
                <i class="fas fa-filter text-4xl mb-3 block opacity-30"></i>
                <p class="text-sm font-medium">No se encontraron productos con esos filtros</p>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: KARDEX DE MOVIMIENTOS -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-slate-800">Kardex de Movimientos</h3>
                <p class="text-xs text-slate-500 mt-0.5">Historial de Entradas, Salidas y Ajustes (últimos 200)</p>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="kardex-search" placeholder="Buscar movimiento..." oninput="filtrarKardex()"
                    class="pl-10 pr-4 py-2 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-accent transition w-60">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Fecha / Hora', 'Producto', 'Talla / Color', 'Movimiento', 'Cantidad', 'Stock Resultante'] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="kardex-table-body">
                    @forelse ($historial as $h)
                        @php
                            $tipo = $h->tipo_movimiento;
                            if ($tipo === 'Entrada') {
                                $colorMov = 'emerald';
                                $signo = '+';
                            } elseif ($tipo === 'Salida') {
                                $colorMov = 'red';
                                $signo = '-';
                            } else {
                                $colorMov = 'amber';
                                $signo = '~';
                            }
                            $dataSearch = strtolower(($h->producto->nombre ?? '') . " " . ($h->producto->talla ?? '') . " " . ($h->producto->color ?? '') . " " . $tipo);
                        @endphp
                        <tr class="trow border-b border-slate-50 text-slate-700" data-search="{{ $dataSearch }}">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-800">{{ $h->fecha_registro ? $h->fecha_registro->format('d/m/Y') : '' }}</p>
                                <p class="text-xs text-slate-400">{{ $h->fecha_registro ? $h->fecha_registro->format('H:i') : '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-sm">{{ $h->producto->nombre ?? 'Producto Eliminado' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                <span class="font-semibold">{{ $h->producto->talla ?? '—' }}</span> <span class="text-slate-400 mx-0.5">/</span> {{ $h->producto->color ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1 text-xs font-semibold text-{{ $colorMov }}-600 bg-{{ $colorMov }}-50 px-2.5 py-1 rounded border border-{{ $colorMov }}-200">
                                    <i class="fas fa-{{ $tipo === 'Entrada' ? 'level-down-alt' : ($tipo === 'Salida' ? 'level-up-alt' : 'exchange-alt') }}"></i> {{ $tipo }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-{{ $colorMov }}-600 text-base">
                                {{ $signo }}{{ $h->cantidad }} <span class="text-xs text-slate-400 font-normal">uds</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-base font-bold text-slate-800">{{ $h->stock_disponible }}</span> <span class="text-xs text-slate-400">uds</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                                <p class="text-sm">No hay movimientos registrados en el Kardex aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="kardex-empty" class="hidden py-14 text-center text-slate-400">
                <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                <p class="text-sm font-medium">No se encontraron movimientos con ese criterio</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Filtro reactivo local de la tabla de Stock actual ───────────────────────
    function filtrarInventario() {
        const q = document.getElementById('inv-search').value.toLowerCase().trim();
        const categoria = document.getElementById('inv-categoria').value.toLowerCase();
        const estado = document.getElementById('inv-estado').value;

        let visibleRows = 0;
        const rows = document.querySelectorAll('#inv-table-body tr.trow-inv');

        rows.forEach(row => {
            const text = row.dataset.search;
            const cat = row.dataset.categoria;
            const stockStatus = row.dataset.stockStatus;

            const matchesQuery = !q || text.includes(q);
            const matchesCategory = !categoria || cat === categoria;
            const matchesStock = estado === 'todos' || stockStatus === estado;

            if (matchesQuery && matchesCategory && matchesStock) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('inv-empty').classList.toggle('hidden', visibleRows > 0);
    }

    function limpiarFiltros() {
        document.getElementById('inv-search').value = '';
        document.getElementById('inv-categoria').value = '';
        document.getElementById('inv-estado').value = 'todos';
        filtrarInventario();
    }

    // ── Filtro reactivo local de la tabla de Kardex ──────────────────────────────
    function filtrarKardex() {
        const q = document.getElementById('kardex-search').value.toLowerCase().trim();
        let visibleRows = 0;
        const rows = document.querySelectorAll('#kardex-table-body tr.trow');

        rows.forEach(row => {
            const text = row.dataset.search;
            if (!q || text.includes(q)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('kardex-empty').classList.toggle('hidden', visibleRows > 0);
    }
</script>
@endpush
