@extends('layouts.sidebaradmin')

@section('titulo', 'Gestión de Clientes')

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
        max-width: 680px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
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

    .toggle {
        width: 44px;
        height: 24px;
        border-radius: 12px;
        position: relative;
        cursor: pointer;
        transition: background .2s;
        display: inline-block;
        flex-shrink: 0;
    }

    .toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: white;
        transition: transform .2s;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
    }

    .toggle.on {
        background: #2563eb;
    }

    .toggle.off {
        background: #cbd5e1;
    }

    .toggle.on::after {
        transform: translateX(20px);
    }

    .gradient-accent {
        background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
    }

    .tab-btn {
        padding: 10px 16px;
        font-size: 14px;
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
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-user-friends text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Gestión de Clientes</h1>
        </div>
    </div>

    <!-- KPIs de Clientes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/25">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Total clientes</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-check text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['activos'] }}</p>
                    <p class="text-xs text-slate-500">Clientes activos</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-shopping-bag text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['compras'] }}</p>
                    <p class="text-xs text-slate-500">Compras totales</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-purple-100 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-purple-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-undo-alt text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['devoluciones'] }}</p>
                    <p class="text-xs text-slate-500">Devoluciones</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Directorio de Clientes -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-4">
            <div>
                <h3 class="font-bold text-slate-800">Clientes registrados</h3>
                <p class="text-xs text-slate-500 mt-0.5">Consulta de datos personales, compras y devoluciones</p>
            </div>
            
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="search-input" placeholder="Buscar por nombre, teléfono..." oninput="filterTable()"
                        class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-accent transition w-72">
                </div>
                <select id="filter-estado" onchange="filterTable()"
                    class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                    <option value="todos">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Cliente', 'Contacto', 'Compras Realizadas', 'Devoluciones', 'Estado', 'Registro', 'Acciones'] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($clientes as $c)
                        @php
                            $ini = strtoupper(substr($c->nombre, 0, 1) . substr($c->apellido, 0, 1));
                            $activo = $c->estado === 'Activo';
                            $dataSearch = strtolower("{$c->nombre} {$c->apellido} {$c->telefono} {$c->email}");
                        @endphp
                        <tr class="trow cursor-pointer text-slate-700" id="row-{{ $c->id }}"
                            data-search="{{ $dataSearch }}"
                            data-estado="{{ $c->estado }}"
                            onclick="openDetalle({{ json_encode($c) }})">
                            
                            <!-- Avatar y Nombre -->
                            <td class="px-6 py-4 border-b border-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 gradient-accent rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ $ini }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $c->nombre }} {{ $c->apellido }}</p>
                                        <p class="text-xs text-slate-400">@ {{ $c->username }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Contacto -->
                            <td class="px-6 py-4 border-b border-slate-50 text-sm">
                                <p class="text-slate-700 text-xs">{{ $c->email }}</p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $c->telefono ?: '—' }}</p>
                            </td>

                            <!-- Compras -->
                            <td class="px-6 py-4 border-b border-slate-50">
                                <span class="badge bg-blue-100 text-blue-700">
                                    <i class="fas fa-shopping-bag text-xs mr-1"></i>{{ $c->ventas_count }} compras
                                </span>
                            </td>

                            <!-- Devoluciones -->
                            <td class="px-6 py-4 border-b border-slate-50">
                                @if ($c->devoluciones_count > 0)
                                    <span class="badge bg-amber-100 text-amber-700">
                                        <i class="fas fa-undo text-xs mr-1"></i>{{ $c->devoluciones_count }} dev.
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs pl-2.5">—</span>
                                @endif
                            </td>

                            <!-- Switch de Estado (con event.stopPropagation para no abrir modal) -->
                            <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2">
                                    <div onclick="toggleEstado(this.parentNode, {{ $c->id }})" class="flex items-center gap-2">
                                        <div class="toggle {{ $activo ? 'on' : 'off' }}"></div>
                                        <span class="text-xs font-semibold text-slate-600 estado-label">{{ $c->estado }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Fecha Registro -->
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">
                                {{ $c->created_at ? $c->created_at->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Acciones -->
                            <td class="px-6 py-4 border-b border-slate-50">
                                <button onclick="event.stopPropagation(); openDetalle({{ json_encode($c) }})" 
                                    class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-users text-4xl mb-3 block opacity-20"></i>
                                <p class="text-sm">No hay clientes registrados aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron clientes con ese criterio</p>
        </div>
    </div>
</div>

<!-- MODAL FICHA DE DETALLES DEL CLIENTE -->
<div id="modal-detalle" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <!-- Cabecera de Ficha -->
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div id="modal-avatar" class="w-12 h-12 gradient-accent rounded-2xl flex items-center justify-center text-white text-base font-bold shadow-md shadow-blue-500/25"></div>
                <div>
                    <h3 id="modal-nombre" class="text-lg font-bold text-slate-800"></h3>
                    <p id="modal-email" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Menú de Pestañas (Tabs) -->
        <div class="flex border-b border-slate-100 px-6 flex-shrink-0">
            <button onclick="switchTab('info', this)" class="tab-btn active"><i class="fas fa-info-circle mr-1"></i>Información</button>
            <button onclick="switchTab('compras', this)" class="tab-btn"><i class="fas fa-shopping-bag mr-1"></i>Compras (<span id="badge-compras">0</span>)</button>
            <button onclick="switchTab('devoluciones', this)" class="tab-btn"><i class="fas fa-undo-alt mr-1"></i>Devoluciones (<span id="badge-devol">0</span>)</button>
        </div>

        <!-- Contenido del Modal -->
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
            
            <!-- PESTAÑA 1: INFORMACIÓN GENERAL -->
            <div id="tab-info" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Nombre Completo</p>
                        <p id="info-nombre" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Nombre de usuario</p>
                        <p id="info-username" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Teléfono</p>
                        <p id="info-tel" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Correo Electrónico</p>
                        <p id="info-email" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Fecha de Registro</p>
                        <p id="info-registro" class="text-sm font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Estado de Cuenta</p>
                        <div class="mt-1 flex items-center justify-between">
                            <span id="info-estado" class="badge"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-center">
                        <p class="text-3xl font-bold text-blue-700" id="info-compras">0</p>
                        <p class="text-xs text-blue-600 font-semibold mt-0.5">Compras Realizadas</p>
                    </div>
                    <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-center">
                        <p class="text-3xl font-bold text-purple-700" id="info-devoluciones">0</p>
                        <p class="text-xs text-purple-600 font-semibold mt-0.5">Devoluciones Solicitadas</p>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA 2: HISTORIAL DE COMPRAS -->
            <div id="tab-compras" class="hidden space-y-3">
                <div id="compras-loading" class="py-12 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 block text-blue-500"></i>
                    <p class="text-xs">Cargando compras...</p>
                </div>
                <div id="lista-compras" class="space-y-3">
                    <!-- Dinámico -->
                </div>
                <div id="no-compras" class="hidden py-12 text-center text-slate-400 bg-white border border-slate-100 rounded-2xl">
                    <i class="fas fa-shopping-bag text-3xl mb-2 block opacity-20"></i>
                    <p class="text-sm">Este cliente no registra compras en su historial.</p>
                </div>
            </div>

            <!-- PESTAÑA 3: HISTORIAL DE DEVOLUCIONES -->
            <div id="tab-devoluciones" class="hidden space-y-3">
                <div id="devol-loading" class="py-12 text-center text-slate-400">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2 block text-amber-500"></i>
                    <p class="text-xs">Cargando devoluciones...</p>
                </div>
                <div id="lista-devoluciones" class="space-y-3">
                    <!-- Dinámico -->
                </div>
                <div id="no-devoluciones" class="hidden py-12 text-center text-slate-400 bg-white border border-slate-100 rounded-2xl">
                    <i class="fas fa-undo-alt text-3xl mb-2 block opacity-20"></i>
                    <p class="text-sm">Este cliente no registra devoluciones en su historial.</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const CLIENTES_URL = "{{ url('/clientes') }}";
    let currentId = null;

    // ── Búsqueda reactiva de clientes en la tabla ──────────────────────────────
    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase().trim();
        const estado = document.getElementById('filter-estado').value;
        const rows = document.querySelectorAll('#table-body tr[id^="row-"]');
        
        let visible = 0;
        
        rows.forEach(row => {
            const matchQ = !q || row.dataset.search.includes(q);
            const matchE = estado === 'todos' || row.dataset.estado === estado;
            const show = matchQ && matchE;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
    }

    // ── Abrir modal de Ficha de Cliente ─────────────────────────────────────────
    function openDetalle(data) {
        currentId = data.id;
        document.getElementById('modal-detalle').classList.remove('hidden');

        // Avatar e Info Básica
        const ini = (data.nombre[0] + (data.apellido ? data.apellido[0] : '')).toUpperCase();
        document.getElementById('modal-avatar').textContent = ini;
        document.getElementById('modal-nombre').textContent = `${data.nombre} ${data.apellido || ''}`;
        document.getElementById('modal-email').textContent = data.email;

        // Ficha General
        document.getElementById('info-nombre').textContent = `${data.nombre} ${data.apellido || ''}`;
        document.getElementById('info-username').textContent = data.username || '—';
        document.getElementById('info-tel').textContent = data.telefono || '—';
        document.getElementById('info-email').textContent = data.email;
        document.getElementById('info-registro').textContent = data.created_at ? data.created_at.substring(0, 10) : '—';
        
        // Conteos badges
        const comprasCount = data.ventas_count ?? 0;
        const devolucionesCount = data.devoluciones_count ?? 0;

        document.getElementById('info-compras').textContent = comprasCount;
        document.getElementById('info-devoluciones').textContent = devolucionesCount;
        document.getElementById('badge-compras').textContent = comprasCount;
        document.getElementById('badge-devol').textContent = devolucionesCount;

        const estadoBadge = document.getElementById('info-estado');
        estadoBadge.textContent = data.estado;
        estadoBadge.className = data.estado === 'Activo' ?
            'badge bg-emerald-100 text-emerald-700' :
            'badge bg-slate-100 text-slate-500';

        // Reiniciar pestañas
        document.getElementById('lista-compras').innerHTML = '';
        document.getElementById('lista-devoluciones').innerHTML = '';
        document.getElementById('no-compras').classList.add('hidden');
        document.getElementById('no-devoluciones').classList.add('hidden');
        document.getElementById('compras-loading').classList.remove('hidden');
        document.getElementById('devol-loading').classList.remove('hidden');

        // Seleccionar pestaña por defecto (Información)
        const tabBtn = document.querySelector('.tab-btn');
        switchTab('info', tabBtn);
    }

    function closeModal() {
        document.getElementById('modal-detalle').classList.add('hidden');
        currentId = null;
    }

    // Cerrar modal al pulsar Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // Cerrar modal al hacer click fuera
    document.getElementById('modal-detalle').addEventListener('click', e => {
        if (e.target === document.getElementById('modal-detalle')) closeModal();
    });

    // ── Alternar Estado del Cliente (Toggle) ──────────────────────────────────
    function toggleEstado(wrapper, id) {
        const toggle = wrapper.querySelector('.toggle');
        const label = wrapper.querySelector('.estado-label');
        const isOn = toggle.classList.contains('on');
        const nuevo = isOn ? 'Inactivo' : 'Activo';

        // Optimismo visual en el switch
        toggle.className = 'toggle ' + (isOn ? 'off' : 'on');
        label.textContent = nuevo;

        const row = document.getElementById(`row-${id}`);
        if (row) row.dataset.estado = nuevo;

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('_method', 'PATCH');

        fetch(`${CLIENTES_URL}/${id}/toggle-estado`, {
            method: 'POST',
            body: fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async r => {
            if (!r.ok) {
                const txt = await r.text();
                throw new Error(txt);
            }
            return r.json();
        })
        .then(d => {
            if (!d.ok) {
                // Revertir en caso de que responda falso
                toggle.className = 'toggle ' + (isOn ? 'on' : 'off');
                label.textContent = isOn ? 'Activo' : 'Inactivo';
                if (row) row.dataset.estado = isOn ? 'Activo' : 'Inactivo';
                showToast(d.msg, 'error');
            } else {
                showToast(d.msg, 'success');
            }
        })
        .catch(err => {
            // Revertir en caso de caída
            toggle.className = 'toggle ' + (isOn ? 'on' : 'off');
            label.textContent = isOn ? 'Activo' : 'Inactivo';
            if (row) row.dataset.estado = isOn ? 'Activo' : 'Inactivo';
            showToast('Error al actualizar el estado del cliente.', 'error');
        });
    }

    // ── Navegación de Pestañas (Tabs) ───────────────────────────────────────────
    function switchTab(tab, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        ['info', 'compras', 'devoluciones'].forEach(t => {
            document.getElementById('tab-' + t).classList.add('hidden');
        });
        document.getElementById('tab-' + tab).classList.remove('hidden');

        if (tab === 'compras' && document.getElementById('lista-compras').innerHTML === '') {
            loadCompras();
        }
        if (tab === 'devoluciones' && document.getElementById('lista-devoluciones').innerHTML === '') {
            loadDevoluciones();
        }
    }

    // ── Cargar compras del cliente via Fetch ──────────────────────────────────
    function loadCompras() {
        if (!currentId) return;
        
        fetch(`${CLIENTES_URL}/${currentId}/compras`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('compras-loading').classList.add('hidden');
            const lista = document.getElementById('lista-compras');
            const noC = document.getElementById('no-compras');

            if (!data.length) {
                noC.classList.remove('hidden');
                return;
            }

            lista.innerHTML = data.map(c => {
                const metodoIcon = c.metodo_pago === 'Efectivo' ? 'money-bill' : 'university';
                return `
                    <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm space-y-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="font-mono text-xs text-slate-400">#${String(c.id_venta).padStart(5, '0')}</span>
                                <p class="font-semibold text-slate-800 text-sm mt-0.5">${c.productos}</p>
                            </div>
                            <span class="badge ${c.estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600'} flex-shrink-0 ml-3">${c.estado}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-50 pt-2">
                            <span><i class="fas fa-calendar-alt mr-1"></i>${c.fecha}</span>
                            <span><i class="fas fa-${metodoIcon} mr-1"></i>${c.metodo_pago}</span>
                            <span class="font-bold text-blue-600 text-sm">$${parseFloat(c.total).toFixed(2)}</span>
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(() => {
            document.getElementById('compras-loading').classList.add('hidden');
        });
    }

    // ── Cargar devoluciones del cliente via Fetch ──────────────────────────────
    function loadDevoluciones() {
        if (!currentId) return;

        fetch(`${CLIENTES_URL}/${currentId}/devoluciones`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('devol-loading').classList.add('hidden');
            const lista = document.getElementById('lista-devoluciones');
            const noD = document.getElementById('no-devoluciones');

            if (!data.length) {
                noD.classList.remove('hidden');
                return;
            }

            lista.innerHTML = data.map(d => `
                <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="font-mono text-xs text-amber-600">DEV-#${String(d.id_devolucion).padStart(3, '0')}</span>
                            <p class="text-sm text-slate-700 mt-0.5">Venta vinculada: <span class="font-semibold text-slate-800">#${String(d.id_venta).padStart(5, '0')}</span></p>
                        </div>
                        <span class="font-bold text-amber-700 text-sm flex-shrink-0 ml-3">$${parseFloat(d.total_devolucion).toFixed(2)}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 border-t border-amber-100/50 pt-2 mt-1">
                        <span class="flex items-center gap-1"><i class="fas fa-comment-alt text-amber-400"></i><em>"${d.motivo}"</em></span>
                        <span><i class="fas fa-calendar-alt mr-1"></i>${d.fecha}</span>
                    </div>
                    <div class="flex justify-end mt-1.5">
                        <span class="badge ${d.estado === 'Aceptada' ? 'bg-emerald-100 text-emerald-700' : (d.estado === 'Rechazada' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700')} text-[10px]">${d.estado}</span>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            document.getElementById('devol-loading').classList.add('hidden');
        });
    }

    // Alertas Toast rápidas mediante SweetAlert2
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
</script>
@endpush
