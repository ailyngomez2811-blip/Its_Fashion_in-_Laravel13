@extends('layouts.sidebaradmin')

@section('titulo', 'Productos')

@push('estilos')
<style>
    /* Efecto hover en las filas de la tabla */
    .trow:hover {
        background: #f8fafc;
    }

    /* Estilo para etiquetas/insignias */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Tarjetas de estadísticas con efecto de elevación */
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

    /* Contenedor del Modal */
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

    /* Caja del Modal */
    .modal-box {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    /* Animación del modal */
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

    /* Gradiente acentuado */
    .gradient-accent {
        background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
    }

    /* Switch tipo Toggle */
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
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-box-open text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Gestión de Productos</h1>
        </div>
        @if (auth()->user()->rol_id === 1)
        <button onclick="openModal('create')" 
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all" 
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Nuevo producto</span>
        </button>
        @endif
    </div>



    <!-- KPIs de Productos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center" style="box-shadow:0 4px 12px rgba(59,130,246,.3);">
                    <i class="fas fa-box-open text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Total productos</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['activos'] }}</p>
                    <p class="text-xs text-slate-500">Activos</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['criticos'] }}</p>
                    <p class="text-xs text-slate-500">Stock crítico</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-red-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['agotados'] }}</p>
                    <p class="text-xs text-slate-500">Agotados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de productos -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Catálogo de productos</h3>
                <p class="text-xs text-slate-500 mt-0.5">Prendas registradas en el sistema</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Buscador local -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="search-input" placeholder="Nombre, talla, color..." oninput="filterTable()" 
                        class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none transition w-64">
                </div>
                <!-- Filtro Categoría -->
                <select id="filter-cat" onchange="filterTable()" 
                    class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <!-- Filtro Estado Stock -->
                <select id="filter-estado" onchange="filterTable()" 
                    class="py-2.5 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                    <option value="critico">Stock crítico</option>
                    <option value="agotado">Agotados</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Producto', 'Categoría', 'Talla / Color', 'Precio Venta', 'Stock', 'Estado', ''] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($productos as $p)
                        @php
                            $activo  = $p->estado === 'Activo';
                            $critico = $p->stock > 0 && $p->stock <= $p->stock_minimo;
                            $agotado = $p->stock == 0;
                            $stockStatus = $agotado ? 'agotado' : ($critico ? 'critico' : 'ok');
                            $esAdmin = auth()->user()->rol_id === 1;
                        @endphp
                        <tr class="trow" 
                            id="row-{{ $p->id }}" 
                            data-search="{{ strtolower($p->nombre . ' ' . $p->talla . ' ' . $p->color . ' ' . ($p->categoria->nombre ?? '')) }}"
                            data-cat="{{ $p->categoria_id }}"
                            data-estado="{{ $p->estado }}"
                            data-stock="{{ $stockStatus }}">

                            <td class="px-6 py-4 border-b border-slate-50">
                                <p class="font-semibold text-slate-800 text-sm">{{ $p->nombre }}</p>
                                <p class="text-xs text-slate-400 truncate max-w-xs">{{ $p->descripcion ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50">
                                <span class="badge bg-blue-100 text-blue-700">{{ $p->categoria->nombre ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">
                                <span class="font-medium">{{ $p->talla }}</span>
                                <span class="text-slate-400 mx-1">/</span>
                                {{ $p->color }}
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm font-semibold text-slate-800">
                                ${{ number_format($p->precio_venta, 2) }}
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50">
                                @if ($agotado)
                                    <span class="badge bg-red-100 text-red-700">
                                        <i class="fas fa-times-circle mr-1 text-xs"></i>Agotado
                                    </span>
                                @elseif ($critico)
                                    <span class="badge bg-amber-100 text-amber-700">
                                        <i class="fas fa-exclamation-triangle mr-1 text-xs"></i>{{ $p->stock }} uds
                                    </span>
                                @else
                                    <span class="badge bg-emerald-100 text-emerald-700">
                                        <i class="fas fa-check mr-1 text-xs"></i>{{ $p->stock }} uds
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                @if ($esAdmin)
                                    <div class="flex items-center gap-2 cursor-pointer" onclick="toggleEstado(this, {{ $p->id }})">
                                        <div class="toggle {{ $activo ? 'on' : 'off' }}"></div>
                                        <span class="text-xs text-slate-600 estado-label">{{ $p->estado }}</span>
                                    </div>
                                @else
                                    <span class="badge {{ $activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $p->estado }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                @if ($esAdmin)
                                    <button onclick="editarProducto({{ $p->id }})"
                                        class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition"
                                        title="Editar producto">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-box-open text-4xl mb-3 block opacity-20"></i>
                                <p class="text-sm">No hay productos registrados aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron productos con ese criterio</p>
        </div>
    </div>
</div>

<!-- MODAL CREAR / EDITAR -->
@if (auth()->user()->rol_id === 1)
<div id="modal-prod" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-7 pb-5 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 gradient-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-box-open text-white text-sm"></i>
                </div>
                <h3 id="modal-title" class="text-lg font-bold text-slate-800">Nuevo producto</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="form-prod" method="POST" action="" class="p-7 overflow-y-auto flex-1 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nombre del producto *</label>
                    <input type="text" name="nombre" id="prod-nombre" required placeholder="Ej: Blusa floral manga larga"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Descripción</label>
                    <textarea name="descripcion" id="prod-desc" rows="2" placeholder="Descripción opcional..."
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 resize-none focus:outline-none transition"></textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Categoría *</label>
                    <select name="categoria_id" id="prod-cat" required class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        <option value="">Seleccionar...</option>
                        @foreach ($categorias as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Estado</label>
                    <select name="estado" id="prod-estado" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Talla *</label>
                    <select name="talla" id="prod-talla" required class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        <option value="">Seleccionar...</option>
                        @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Única'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Color *</label>
                    <input type="text" name="color" id="prod-color" required placeholder="Ej: Azul marino"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Precio Compra *</label>
                    <input type="number" name="precio_compra" id="prod-pc" required min="0" step="0.01" placeholder="0.00"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Precio Venta *</label>
                    <input type="number" name="precio_venta" id="prod-pv" required min="0" step="0.01" placeholder="0.00"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Stock Inicial *</label>
                    <input type="number" name="stock" id="prod-stock" required min="0" placeholder="0"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" id="prod-smin" min="0" placeholder="0"
                        class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button type="submit" class="flex-1 py-3 gradient-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-save mr-2 text-xs"></i><span id="btn-submit-text">Guardar producto</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<!-- SweetAlert2 para mensajes de alerta -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const STORE_URL = "{{ route('productos.store') }}";
    const BASE_URL = "{{ url('/productos') }}";

    // ── Filtros en cliente ────────────────────────────────────────────────────────
    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        const cat = document.getElementById('filter-cat').value;
        const estado = document.getElementById('filter-estado').value;
        let visible = 0;

        document.querySelectorAll('#table-body tr[id^="row-"]').forEach(row => {
            const matchQ = !q || row.dataset.search.includes(q);
            const matchC = !cat || row.dataset.cat === cat;
            
            let matchE = true;
            if (estado === 'Activo' || estado === 'Inactivo') {
                matchE = row.dataset.estado === estado;
            } else if (estado === 'critico') {
                matchE = row.dataset.stock === 'critico';
            } else if (estado === 'agotado') {
                matchE = row.dataset.stock === 'agotado';
            }

            const show = matchQ && matchC && matchE;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
    }

    // ── Alternar estado vía AJAX ───────────────────────────────────────────────────
    function toggleEstado(el, id) {
        const toggle = el.querySelector('.toggle');
        const label = el.querySelector('.estado-label');
        const isOn = toggle.classList.contains('on');
        const nuevo = isOn ? 'Inactivo' : 'Activo';

        fetch(`${BASE_URL}/${id}/toggle-estado`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ estado: nuevo })
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                toggle.classList.toggle('on', !isOn);
                toggle.classList.toggle('off', isOn);
                label.textContent = nuevo;
                const row = document.getElementById('row-' + id);
                if (row) row.dataset.estado = nuevo;
                showToast('Estado actualizado', 'success');
            } else {
                showToast(d.msg || 'Error al cambiar estado', 'error');
            }
        });
    }

    // ── Modal Apertura / Cierre ──────────────────────────────────────────────────
    @if (auth()->user()->rol_id === 1)
    function openModal(mode, data = null) {
        const modal = document.getElementById('modal-prod');
        const form = document.getElementById('form-prod');
        const methodInput = document.getElementById('form-method');
        const title = document.getElementById('modal-title');
        const btnText = document.getElementById('btn-submit-text');

        form.reset();

        if (mode === 'create') {
            form.action = STORE_URL;
            methodInput.value = 'POST';
            title.textContent = 'Nuevo producto';
            btnText.textContent = 'Guardar producto';
            modal.classList.remove('hidden');
        }
    }

    function closeModal() {
        document.getElementById('modal-prod').classList.add('hidden');
    }

    // Editar producto vía consulta AJAX (para poblar los campos del modal)
    function editarProducto(id) {
        const modal = document.getElementById('modal-prod');
        const form = document.getElementById('form-prod');
        const methodInput = document.getElementById('form-method');
        const title = document.getElementById('modal-title');
        const btnText = document.getElementById('btn-submit-text');

        fetch(`${BASE_URL}/${id}`)
            .then(r => r.json())
            .then(p => {
                if (!p) return;
                
                form.action = `${BASE_URL}/${id}`;
                methodInput.value = 'PUT';
                
                document.getElementById('prod-nombre').value = p.nombre;
                document.getElementById('prod-desc').value = p.descripcion || '';
                document.getElementById('prod-cat').value = p.categoria_id;
                document.getElementById('prod-estado').value = p.estado;
                document.getElementById('prod-talla').value = p.talla;
                document.getElementById('prod-color').value = p.color;
                document.getElementById('prod-pc').value = p.precio_compra;
                document.getElementById('prod-pv').value = p.precio_venta;
                document.getElementById('prod-stock').value = p.stock;
                document.getElementById('prod-smin').value = p.stock_minimo;
                
                title.textContent = 'Editar producto';
                btnText.textContent = 'Actualizar producto';
                modal.classList.remove('hidden');
            });
    }

    // Escuchar el evento Escape para cerrar el modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // Control de foco visual en inputs
    document.querySelectorAll('#form-prod input, #form-prod select, #form-prod textarea').forEach(el => {
        el.addEventListener('focus', () => {
            el.style.borderColor = '#3b82f6';
            el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.15)';
        });
        el.addEventListener('blur', () => {
            el.style.borderColor = '';
            el.style.boxShadow = '';
        });
    });
    @endif

    // Redefinido para usar SweetAlert2 en modo Toast
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

    // Mostrar Toast si existe sesión de redirección con "toast" de Laravel
    @if (session('toast'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast(@json(session('toast')['text']), @json(session('toast')['type']));
        });
    @endif

    // Mostrar errores de validación con SweetAlert2
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Entendido'
            });
        });
    @endif
</script>
@endpush
