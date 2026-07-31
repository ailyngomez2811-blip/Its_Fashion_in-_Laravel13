@extends('layouts.Sidebarempleado')

@section('titulo', 'Inventario y Kardex (Empleado)')

@section('content')
<div class="p-6 md:p-8 font-sans max-w-[1600px] mx-auto">
    <!-- Cabecera -->
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                <i class="fas fa-warehouse text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Inventario</h1>
                <p class="text-brand-muted text-xs font-light">Stock actual y registro de movimientos de Kardex</p>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500">
                    <i class="fas fa-box text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['total'] }}</p>
                    <p class="text-xs text-slate-500">Prendas en catálogo</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['conStock'] }}</p>
                    <p class="text-xs text-slate-500">Stock suficiente</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['critico'] }}</p>
                    <p class="text-xs text-slate-500">Stock crítico</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center text-red-500">
                    <i class="fas fa-times-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['sinStock'] }}</p>
                    <p class="text-xs text-slate-500">Agotado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación por pestañas (Tabs) estilo Premium -->
    <div class="mb-6 flex border-b border-slate-200">
        <button onclick="switchTab('stock')" id="tab-stock" class="px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-boxes"></i> Existencias en Bodega
        </button>
        <button onclick="switchTab('kardex')" id="tab-kardex" class="px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-history"></i> Kardex (Historial de Movimientos)
        </button>
    </div>

    <!-- SECCIÓN: TABLA DE STOCK ACTUAL -->
    <div id="section-stock" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
            <div class="p-6 border-b border-slate-100 flex flex-col gap-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800">Existencias en Bodega</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Control de prendas y categorías</p>
                    </div>
                </div>

                <!-- Filtros de Existencias -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input type="text" id="inv-search" placeholder="Nombre de prenda..." oninput="filtrarInventario()"
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
                            @foreach (['Prenda', 'Categoría', 'Talla/Color', 'Stock', 'Mínimo', 'Estado'] as $h)
                                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="inv-table-body" class="divide-y divide-slate-50">
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
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $p->nombre }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $p->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $p->talla }} / {{ $p->color }}</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full {{ $badgeStock }}">
                                        <i class="fas {{ $iconStock }}"></i> {{ $p->stock }} uds
                                    </span>
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $p->stock_minimo ?? '0' }} uds</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="badge text-xs font-semibold px-2.5 py-1 rounded-full {{ $p->estado === 'Activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $p->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <i class="fas fa-warehouse text-4xl mb-3 block opacity-20"></i>
                                    <p class="text-sm">No hay prendas registradas en inventario</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="inv-empty" class="hidden py-14 text-center text-slate-400">
                    <i class="fas fa-filter text-4xl mb-3 block opacity-30"></i>
                    <p class="text-sm font-medium">No se encontraron productos</p>
                </div>
            </div>
            
            <!-- Paginador de Inventario (Empleado) -->
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
                            @foreach (['Fecha', 'Prenda', 'Tipo', 'Cantidad', 'Stock final'] as $h)
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="kardex-table-body" class="divide-y divide-slate-50">
                        @forelse ($historial as $h)
                            @php
                                $tipo = $h->tipo_movimiento;
                                $colorMov = $tipo === 'Ingreso' ? 'emerald' : 'red';
                                $signo = $tipo === 'Ingreso' ? '+' : '-';
                                $dataSearch = strtolower(($h->producto->nombre ?? '') . " " . $tipo);
                            @endphp
                            <tr class="trow border-b border-slate-50 text-slate-700" data-search="{{ $dataSearch }}">
                                <td class="px-5 py-3.5 text-sm text-slate-500">
                                    {{ $h->fecha_registro ? $h->fecha_registro->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-bold text-slate-800 text-sm">{{ $h->producto->nombre ?? 'Prenda Eliminada' }}</p>
                                    <p class="text-[10px] text-slate-400 font-light">{{ $h->producto->talla ?? '—' }} / {{ $h->producto->color ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-{{ $colorMov }}-600 bg-{{ $colorMov }}-50 px-2 py-0.5 rounded border border-{{ $colorMov }}-200">
                                        {{ $tipo }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm font-bold text-{{ $colorMov }}-600">{{ $signo }}{{ $h->cantidad }}</td>
                                <td class="px-5 py-3.5 text-sm font-bold text-slate-800">{{ $h->stock_disponible }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                    <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                                    <p class="text-sm">No se han registrado movimientos de inventario aún</p>
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
            
            <!-- Paginador de Kardex (Empleado) -->
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
</script>
@endpush
