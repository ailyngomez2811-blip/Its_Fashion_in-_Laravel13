@extends('layouts.sidebaradmin')

@section('titulo', 'Inventario y Kardex')

@section('estilos')
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
@endsection

@section('content')
<div class="p-6 font-sans max-w-[1600px] mx-auto">

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

    <!-- Navegación por pestañas (Tabs) estilo Premium -->
    <div class="mb-6 flex border-b border-slate-200">
        <button onclick="switchTab('stock')" id="tab-stock" class="px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-boxes"></i> Inventario Actual
        </button>
        <button onclick="switchTab('kardex')" id="tab-kardex" class="px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-history"></i> Kardex de Movimientos
        </button>
    </div>

    <!-- SECCIÓN: TABLA DE INVENTARIO Y STOCK ACTUAL -->
    <div id="section-stock" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <div class="p-6 border-b border-slate-100 flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800">Existencias en Bodega</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Control de almacén y precios de venta</p>
                    </div>
                    <button onclick="limpiarFiltros()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                        <i class="fas fa-undo text-[10px]"></i> Limpiar filtros
                    </button>
                </div>

                <!-- Filtros de Existencias -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input type="text" id="inv-search" placeholder="Buscar por prenda, talla..." oninput="filtrarInventario()"
                            class="w-full pl-10 pr-4 py-2 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-brand-accent transition">
                    </div>
                    <div>
                        <select id="inv-categoria" onchange="filtrarInventario()"
                            class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $cat)
                            <option value="{{ strtolower($cat->nombre) }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select id="inv-estado" onchange="filtrarInventario()"
                            class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                            <option value="todos">Todos los stocks</option>
                            <option value="con_stock">Con stock</option>
                            <option value="critico">Stock crítico</option>
                            <option value="sin_stock">Sin stock</option>
                        </select>
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
                            'critico' => 'bg-amber-100 text-amber-700',
                            default => 'bg-emerald-100 text-emerald-700',
                        };
                        $iconStock = match($stockStatus) {
                            'sin_stock' => 'fa-times-circle',
                            'critico' => 'fa-exclamation-triangle',
                            default => 'fa-check-circle',
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
            
            <!-- Paginador de Inventario -->
            <div id="inv-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="inv-page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button onclick="prevInvPage()" id="btn-inv-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button onclick="nextInvPage()" id="btn-inv-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: KARDEX DE MOVIMIENTOS -->
    <div id="section-kardex" class="space-y-4 hidden">
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
            
            <!-- Paginador de Kardex -->
            <div id="kardex-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="kardex-page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button onclick="prevKardexPage()" id="btn-kardex-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button onclick="nextKardexPage()" id="btn-kardex-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let invPage = 1;
    let kardexPage = 1;
    const recordsPerPage = 10;

    document.addEventListener('DOMContentLoaded', () => {
        applyPagination('inv');
        applyPagination('kardex');
    });

    function switchTab(tab) {
        const tabStock = document.getElementById('tab-stock');
        const tabKardex = document.getElementById('tab-kardex');
        const secStock = document.getElementById('section-stock');
        const secKardex = document.getElementById('section-kardex');

        if (tab === 'stock') {
            tabStock.className = "px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300 flex items-center gap-2";
            tabKardex.className = "px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300 flex items-center gap-2";
            secStock.classList.remove('hidden');
            secKardex.classList.add('hidden');
            applyPagination('inv');
        } else {
            tabKardex.className = "px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300 flex items-center gap-2";
            tabStock.className = "px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300 flex items-center gap-2";
            secKardex.classList.remove('hidden');
            secStock.classList.add('hidden');
            applyPagination('kardex');
        }
    }

    function applyPagination(type) {
        let rows, page, infoSpan, prevBtn, nextBtn;
        
        if (type === 'inv') {
            rows = Array.from(document.querySelectorAll('.trow-inv')).filter(row => row.style.display !== 'none');
            page = invPage;
            infoSpan = document.getElementById('inv-page-info');
            prevBtn = document.getElementById('btn-inv-prev');
            nextBtn = document.getElementById('btn-inv-next');
        } else {
            rows = Array.from(document.querySelectorAll('#kardex-table-body tr.trow')).filter(row => row.style.display !== 'none');
            page = kardexPage;
            infoSpan = document.getElementById('kardex-page-info');
            prevBtn = document.getElementById('btn-kardex-prev');
            nextBtn = document.getElementById('btn-kardex-next');
        }

        const total = rows.length;
        const totalPages = Math.ceil(total / recordsPerPage) || 1;
        
        if (page > totalPages) {
            page = totalPages;
            if (type === 'inv') invPage = page; else kardexPage = page;
        }

        const startIdx = (page - 1) * recordsPerPage;
        const endIdx = startIdx + recordsPerPage;

        rows.forEach((row, idx) => {
            if (idx >= startIdx && idx < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Actualizar estados de los controles
        if (total === 0) {
            infoSpan.textContent = "Mostrando registros 0 de 0";
            prevBtn.disabled = true;
            nextBtn.disabled = true;
        } else {
            infoSpan.textContent = `Mostrando registros ${startIdx + 1}-${Math.min(endIdx, total)} de ${total}`;
            prevBtn.disabled = page === 1;
            nextBtn.disabled = page === totalPages;
        }
    }

    function prevInvPage() {
        if (invPage > 1) {
            invPage--;
            applyPagination('inv');
        }
    }

    function nextInvPage() {
        invPage++;
        applyPagination('inv');
    }

    function prevKardexPage() {
        if (kardexPage > 1) {
            kardexPage--;
            applyPagination('kardex');
        }
    }

    function nextKardexPage() {
        kardexPage++;
        applyPagination('kardex');
    }

    // Filtro reactivo local de la tabla de Stock actual
    function filtrarInventario() {
        const query = document.getElementById('inv-search').value.toLowerCase().trim();
        const categoria = document.getElementById('inv-categoria').value;
        const stockStatus = document.getElementById('inv-estado').value;

        const rows = document.querySelectorAll('.trow-inv');
        let visibleCount = 0;

        rows.forEach(row => {
            const matchesQuery = !query || row.dataset.search.includes(query);
            const matchesCat = !categoria || row.dataset.categoria === categoria;
            const matchesStatus = stockStatus === 'todos' || row.dataset.stockStatus === stockStatus;

            if (matchesQuery && matchesCat && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('inv-empty').classList.toggle('hidden', visibleCount > 0);
        invPage = 1;
        applyPagination('inv');
    }

    // Filtro reactivo local de la tabla de Kardex
    function filtrarKardex() {
        const query = document.getElementById('kardex-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#kardex-table-body tr.trow');
        let visibleCount = 0;

        rows.forEach(row => {
            const matchesQuery = !query || row.dataset.search.includes(query);

            if (matchesQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('kardex-empty').classList.toggle('hidden', visibleCount > 0);
        kardexPage = 1;
        applyPagination('kardex');
    }

    function limpiarFiltros() {
        document.getElementById('inv-search').value = '';
        document.getElementById('inv-categoria').value = '';
        document.getElementById('inv-estado').value = 'todos';
        filtrarInventario();
    }
</script>
@endpush