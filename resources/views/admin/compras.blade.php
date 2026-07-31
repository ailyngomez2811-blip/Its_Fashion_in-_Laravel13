@extends('layouts.sidebaradmin')

@section('titulo', 'Abastecimiento')

@push('estilos')
<style>
    /* Efecto hover en las filas de la tabla */
    .trow:hover {
        background: #f8fafc;
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

    /* Caja de modal para nueva compra */
    .modal-box {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 760px;
        max-height: 94vh;
        display: flex;
        flex-direction: column;
    }

    /* Caja de modal para ver detalles */
    .modal-det {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 25px 60px rgba(0, 0, 0, .15);
        width: 100%;
        max-width: 600px;
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
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-truck text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Abastecimiento (Compras)</h1>
        </div>
        <button onclick="openModal()"
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all"
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Registrar compra</span>
        </button>
    </div>

    <!-- KPIs de Compras -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.3);">
                    <i class="fas fa-receipt text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Total de compras</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($kpi['monto'], 2) }}</p>
                    <p class="text-xs text-slate-500">Monto invertido</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['hoy'] }}</p>
                    <p class="text-xs text-slate-500">Compras registradas hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Directorio de compras -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Historial de abastecimiento</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ count($compras) }} compra(s) registrada(s)</p>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" id="search-input" placeholder="Buscar proveedor..." oninput="filterTable()"
                    class="pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none transition w-64">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['ID', 'Fecha', 'Proveedor', 'Registrado Por', 'Total', 'Acciones'] as $h)
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($compras as $c)
                    <tr class="trow cursor-pointer" onclick="verDetalle({{ $c->id }})" data-search="{{ strtolower($c->proveedor->nombre ?? '') }}">
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#{{ $c->id }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $c->fecha instanceof \Carbon\Carbon ? $c->fecha->format('d/m/Y H:i') : ($c->fecha ? date('d/m/Y H:i', strtotime($c->fecha)) : '—') }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-semibold text-slate-800">{{ $c->proveedor->nombre ?? '—' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $c->usuario->name ?? '—' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">${{ number_format($c->total, 2) }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-xs text-blue-500">
                            <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center hover:bg-blue-100 transition">
                                <i class="fas fa-eye text-sm"></i>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-truck text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">No hay compras registradas aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron compras</p>
        </div>
    </div>
</div>

<!-- MODAL REGISTRAR NUEVA COMPRA (CARRITO) -->
<div id="modal-compra" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-truck text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Nueva compra (Abastecimiento)</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Proveedor *</label>
                <select id="sel-proveedor" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <option value="">Seleccionar proveedor...</option>
                    @foreach ($proveedores as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->contacto }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Agregar producto al detalle</label>
                <div class="flex flex-wrap gap-2">
                    <select id="sel-producto" class="flex-1 min-w-[200px] px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        <option value="">Seleccionar producto...</option>
                        @foreach ($productos as $p)
                        <option value="{{ $p->id }}" data-precio="{{ $p->precio_compra }}">
                            {{ $p->nombre }} ({{ $p->talla }}/{{ $p->color }})
                        </option>
                        @endforeach
                    </select>
                    <input type="number" id="inp-qty" placeholder="Cant." min="1"
                        class="w-20 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <input type="number" id="inp-precio" placeholder="Precio" min="0" step="0.01"
                        class="w-28 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <button onclick="agregarItem()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-md transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Listado dinámico del carrito -->
            <div id="items-list" class="space-y-2 border-t border-slate-100 pt-3">
                <!-- Se dibuja por JS -->
            </div>

            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                <span class="font-semibold text-slate-700">Total compra</span>
                <span id="total-display" class="text-xl font-bold text-blue-600">$0.00</span>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="guardarCompra()" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-save mr-2 text-xs"></i>Registrar compra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VER DETALLE COMPRA -->
<div id="modal-det" class="modal-overlay hidden">
    <div class="modal-det scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-800">Detalle de compra</h3>
            <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="det-body" class="p-6 overflow-y-auto flex-1">
            <!-- Se dibuja mediante FETCH AJAX -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const STORE_URL = "{{ route('compras.store') }}";
    const BASE_URL = "{{ url('/compras') }}";
    let items = [];

    // ── Auto-rellenar precio de compra al seleccionar producto ───────────────────
    document.getElementById('sel-producto').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        document.getElementById('inp-precio').value = opt.dataset.precio || '';
    });

    // ── Filtrado local reactivo ────────────────────────────────────────────────────
    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        let visible = 0;

        document.querySelectorAll('#table-body tr').forEach(row => {
            if (row.dataset.search) {
                const show = !q || row.dataset.search.includes(q);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            }
        });

        document.getElementById('empty-state').classList.toggle('hidden', visible > 0);
    }

    // ── Control de Modales ─────────────────────────────────────────────────────────
    function openModal() {
        items = [];
        document.getElementById('sel-proveedor').value = "";
        document.getElementById('sel-producto').value = "";
        document.getElementById('inp-qty').value = "";
        document.getElementById('inp-precio').value = "";
        renderItems();
        document.getElementById('modal-compra').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-compra').classList.add('hidden');
    }

    function closeDet() {
        document.getElementById('modal-det').classList.add('hidden');
    }

    // Cierre del modal con tecla Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeModal();
            closeDet();
        }
    });

    // ── Lógica del Carrito ─────────────────────────────────────────────────────────
    function agregarItem() {
        const sel = document.getElementById('sel-producto');
        const id = parseInt(sel.value);
        const qty = parseInt(document.getElementById('inp-qty').value);
        const precio = parseFloat(document.getElementById('inp-precio').value);
        const nombre = sel.options[sel.selectedIndex]?.text.trim();

        if (!id || !qty || qty < 1 || isNaN(precio) || precio <= 0) {
            showToast('Completa el producto, cantidad y precio válido.', 'error');
            return;
        }

        const idx = items.findIndex(i => i.id_producto === id);
        if (idx >= 0) {
            items[idx].cantidad += qty;
        } else {
            items.push({
                id_producto: id,
                nombre: nombre,
                cantidad: qty,
                precio_unitario: precio
            });
        }

        renderItems();
        document.getElementById('sel-producto').value = '';
        document.getElementById('inp-qty').value = '';
        document.getElementById('inp-precio').value = '';
    }

    function renderItems() {
        const list = document.getElementById('items-list');
        const total = items.reduce((s, i) => s + i.cantidad * i.precio_unitario, 0);
        document.getElementById('total-display').textContent = '$' + total.toFixed(2);

        if (!items.length) {
            list.innerHTML = '<p class="text-xs text-slate-400 text-center py-4">Sin productos agregados</p>';
            return;
        }

        list.innerHTML = items.map((it, idx) => `
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl text-sm">
                <span class="font-medium text-slate-700 flex-1 truncate">${it.nombre}</span>
                <span class="text-slate-500 mx-3">${it.cantidad} × $${it.precio_unitario.toFixed(2)}</span>
                <span class="font-bold text-blue-700 mr-3">$${(it.cantidad * it.precio_unitario).toFixed(2)}</span>
                <button onclick="quitarItem(${idx})" class="w-6 h-6 bg-red-100 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-200 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        `).join('');
    }

    function quitarItem(idx) {
        items.splice(idx, 1);
        renderItems();
    }

    // ── Guardar Compra ─────────────────────────────────────────────────────────────
    function guardarCompra() {
        const id_proveedor = document.getElementById('sel-proveedor').value;

        if (!id_proveedor) {
            showToast('Selecciona un proveedor', 'error');
            return;
        }
        if (!items.length) {
            showToast('Agrega al menos un producto al detalle', 'error');
            return;
        }

        const fd = new FormData();
        fd.append('proveedor_id', id_proveedor);
        fd.append('items', JSON.stringify(items));
        fd.append('_token', '{{ csrf_token() }}');

        fetch(STORE_URL, {
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
                if (d.ok) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Compra Registrada!',
                        text: d.msg,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Excelente'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atención',
                        text: d.msg,
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Servidor',
                    text: err.message.substring(0, 200) || 'Ocurrió un error inesperado al procesar la compra.'
                });
            });
    }

    // ── Ver detalles de compra (FETCH AJAX) ─────────────────────────────────────────
    function verDetalle(id) {
        // Prevenir que haga trigger el modal al hacer click en acciones secundarias
        fetch(`${BASE_URL}/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(detalles => {
                const body = document.getElementById('det-body');
                if (!detalles.length) {
                    body.innerHTML = '<p class="text-slate-400 text-sm text-center py-8">Sin detalle disponible</p>';
                } else {
                    body.innerHTML = `
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="py-2 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                                    <th class="py-2 text-xs font-semibold text-slate-500 uppercase">Talla/Color</th>
                                    <th class="py-2 text-xs font-semibold text-slate-500 uppercase text-right">Cant.</th>
                                    <th class="py-2 text-xs font-semibold text-slate-500 uppercase text-right">P. Unit</th>
                                    <th class="py-2 text-xs font-semibold text-slate-500 uppercase text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${detalles.map(i => `
                                    <tr class="border-b border-slate-50 trow">
                                        <td class="py-3 font-medium text-slate-800">${i.producto}</td>
                                        <td class="py-3 text-slate-500">${i.talla}/${i.color}</td>
                                        <td class="py-3 text-right text-slate-700">${i.cantidad}</td>
                                        <td class="py-3 text-right text-slate-700">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                                        <td class="py-3 text-right font-bold text-blue-700">$${parseFloat(i.subtotal).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                }
                document.getElementById('modal-det').classList.remove('hidden');
            })
            .catch(err => {
                showToast('Error al cargar detalles de la compra.', 'error');
            });
    }

    // Alertas Toast mediante SweetAlert2
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

    // Notificaciones de Laravel session redirect
    @if(session('toast'))
    document.addEventListener('DOMContentLoaded', () => {
        showToast(@json(session('toast')['text']), @json(session('toast')['type']));
    });
    @endif
</script>
@endpush