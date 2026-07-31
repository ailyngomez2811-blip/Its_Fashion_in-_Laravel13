@extends('layouts.Sidebarempleado')

@section('titulo', 'Inventario y Kardex (Empleado)')

@section('content')
<div class="p-6 md:p-8 font-sans">
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
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['total'] }}</p>
                    <p class="text-xs text-slate-500">Prendas en catálogo</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-circle"></i>
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
                    <i class="fas fa-exclamation-triangle"></i>
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
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpis['sinStock'] }}</p>
                    <p class="text-xs text-slate-500">Agotado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="mb-6 flex border-b border-slate-200">
        <button onclick="switchTab('stock')" id="tab-stock" class="px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300">
            Existencias en Bodega
        </button>
        <button onclick="switchTab('kardex')" id="tab-kardex" class="px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300">
            Kardex (Historial de Movimientos)
        </button>
    </div>

    <!-- Sección Stock -->
    <div id="section-stock" class="space-y-4">
        <!-- Filtros Stock -->
        <div class="bg-white rounded-[1.5rem] border border-slate-100 p-5 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Búsqueda rápida</label>
                    <input type="text" id="stock-search" oninput="filterStock()" placeholder="Nombre de prenda..." 
                        class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Categoría</label>
                    <select id="stock-cat" onchange="filterStock()" 
                        class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->nombre }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Estado de stock</label>
                    <select id="stock-status" onchange="filterStock()" 
                        class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                        <option value="">Todos los niveles</option>
                        <option value="Disponible">Disponible</option>
                        <option value="Crítico">Crítico</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr>
                            @foreach (['Prenda', 'Categoría', 'Talla/Color', 'Stock', 'Mínimo', 'Estado'] as $h)
                                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="stock-tbody">
                        @forelse ($productos as $p)
                            @php
                                $estado = 'Disponible';
                                $badgeCls = 'bg-emerald-100 text-emerald-700';
                                if ($p->stock == 0) {
                                    $estado = 'Agotado';
                                    $badgeCls = 'bg-red-100 text-red-700';
                                } elseif ($p->stock <= $p->stock_minimo) {
                                    $estado = 'Crítico';
                                    $badgeCls = 'bg-amber-100 text-amber-700';
                                }
                            @endphp
                            <tr class="trow text-slate-700" 
                                data-nombre="{{ strtolower($p->nombre) }}"
                                data-categoria="{{ $p->categoria->nombre ?? '' }}"
                                data-estado="{{ $estado }}">
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $p->nombre }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $p->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $p->talla }} / {{ $p->color }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">{{ $p->stock }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $p->stock_minimo ?? '0' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="badge {{ $badgeCls }}">{{ $estado }}</span>
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
            </div>
        </div>
    </div>

    <!-- Sección Kardex -->
    <div id="section-kardex" class="space-y-4 hidden">
        <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Historial de movimientos</h3>
                <p class="text-xs text-slate-500 mt-0.5">Últimos 200 ingresos o egresos de mercancía</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr>
                            @foreach (['Fecha', 'Prenda', 'Detalles', 'Tipo', 'Cantidad', 'Stock final'] as $h)
                                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($historial as $h)
                            <tr class="trow text-slate-700">
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $h->fecha_registro ? $h->fecha_registro->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $h->producto->nombre ?? 'Prenda Eliminada' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $h->descripcion }}</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="badge {{ $h->tipo_movimiento === 'Ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $h->tipo_movimiento }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">{{ $h->cantidad }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">{{ $h->stock_resultante }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                                    <p class="text-sm">No se han registrado movimientos de inventario aún</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        const tabStock = document.getElementById('tab-stock');
        const tabKardex = document.getElementById('tab-kardex');
        const secStock = document.getElementById('section-stock');
        const secKardex = document.getElementById('section-kardex');

        if (tab === 'stock') {
            tabStock.className = "px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300";
            tabKardex.className = "px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300";
            secStock.classList.remove('hidden');
            secKardex.classList.add('hidden');
        } else {
            tabKardex.className = "px-6 py-3 text-sm font-semibold border-b-2 border-brand-accent text-brand-accent focus:outline-none transition-all duration-300";
            tabStock.className = "px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none transition-all duration-300";
            secKardex.classList.remove('hidden');
            secStock.classList.add('hidden');
        }
    }

    function filterStock() {
        const query = document.getElementById('stock-search').value.toLowerCase().trim();
        const cat = document.getElementById('stock-cat').value;
        const status = document.getElementById('stock-status').value;

        const rows = document.querySelectorAll('#stock-tbody tr');
        rows.forEach(row => {
            if (!row.dataset.nombre) return;
            const matchesQuery = row.dataset.nombre.includes(query);
            const matchesCat = !cat || row.dataset.categoria === cat;
            const matchesStatus = !status || row.dataset.estado === status;
            
            row.style.display = (matchesQuery && matchesCat && matchesStatus) ? '' : 'none';
        });
    }
</script>
@endpush
@endsection
