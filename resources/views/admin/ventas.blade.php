@extends('layouts.sidebaradmin')

@section('titulo', 'Ventas')

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

    /* Caja de modal para nueva venta */
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

    /* Estilo para etiquetas/insignias */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-shopping-cart text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Ventas</h1>
        </div>

        @if ($cajaAbierta)
        <button onclick="openModal()"
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all"
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Nueva venta</span>
        </button>
        @else
        <button onclick="mostrarAdvertenciaCaja()"
            class="flex items-center gap-2 px-4 py-2.5 bg-slate-400 text-white text-sm font-semibold rounded-xl cursor-not-allowed hover:shadow-md transition-all">
            <i class="fas fa-plus text-xs"></i> <span>Nueva venta (Caja cerrada)</span>
        </button>
        @endif
    </div>

    <!-- KPIs de Ventas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 gradient-accent rounded-xl flex items-center justify-center shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.3);">
                    <i class="fas fa-shopping-bag text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800" id="cant-total">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Ventas totales</p>
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
                    <p class="text-xs text-slate-500">Ingresos totales</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-blue-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-white"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['completadas'] }}</p>
                    <p class="text-xs text-slate-500">Ventas completadas</p>
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
                    <p class="text-xs text-slate-500">Ventas hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Directorio de Ventas -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="p-6 border-b border-slate-100 flex flex-col gap-4">
            <div class="flex justify-between items-center flex-wrap gap-2">
                <div>
                    <h3 class="font-bold text-slate-800">Historial de ventas</h3>
                    <p class="text-xs text-slate-500 mt-0.5"><span id="cant-filtrada">{{ count($ventas) }}</span> venta(s) encontrada(s)</p>
                </div>
            </div>

            <!-- Controles de filtrado local -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Fecha desde</label>
                    <input type="date" id="filter-desde" onchange="filterTable()"
                        class="w-full py-2 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Fecha hasta</label>
                    <input type="date" id="filter-hasta" onchange="filterTable()"
                        class="w-full py-2 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Método de pago</label>
                    <select id="filter-metodo" onchange="filterTable()"
                        class="w-full py-2 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                        <option value="">Todos los métodos</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia bancaria">Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Estado</label>
                    <select id="filter-estado" onchange="filterTable()"
                        class="w-full py-2 px-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                        <option value="">Todos los estados</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['#', 'Fecha', 'Cliente', 'Empleado', 'Método', 'Total', 'Estado', 'Acciones'] as $h)
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($ventas as $v)
                    <tr class="trow cursor-pointer text-slate-700" id="vrow-{{ $v->id }}"
                        data-metodo="{{ $v->metodo_pago }}"
                        data-estado="{{ $v->estado }}"
                        data-fecha="{{ $v->fecha ? $v->fecha->format('Y-m-d') : '' }}"
                        onclick="verDetalle({{ $v->id }})">
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#{{ $v->id }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $v->cliente->nombre ?? 'Mostrador' }} {{ $v->cliente->apellido ?? '' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $v->usuario->nombre ?? '—' }} {{ $v->usuario->apellido ?? '' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <span class="badge {{ $v->metodo_pago === 'Efectivo' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                <i class="fas {{ $v->metodo_pago === 'Efectivo' ? 'fa-money-bill' : 'fa-university' }} mr-1 text-xs"></i>
                                {{ $v->metodo_pago }}
                            </span>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">${{ number_format($v->total, 2) }}</td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <span class="badge {{ $v->estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $v->estado }}</span>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                            <div class="flex items-center gap-2">
                                <button onclick="verDetalle({{ $v->id }})" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition" title="Ver detalle">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                @if ($v->estado === 'Completada')
                                <button onclick="openDevolucion({{ $v->id }})" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg flex items-center justify-center transition" title="Solicitar Devolución">
                                    <i class="fas fa-undo-alt text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-shopping-cart text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">No hay ventas registradas aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Paginador de Ventas -->
            <div id="sales-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="sales-page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevSalesPage()" id="btn-sales-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button type="button" onclick="nextSalesPage()" id="btn-sales-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron ventas con ese criterio</p>
        </div>
    </div>
</div>

<!-- MODAL NUEVA VENTA (CARRITO DE VENTAS) -->
@if ($cajaAbierta)
<div id="modal-venta" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Nueva Venta</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 space-y-4">
            <!-- Buscar cliente (Autocompletado) -->
            <div class="relative">
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Cliente (opcional)</label>
                <div class="flex gap-2">
                    <input type="text" id="inp-cliente" placeholder="Buscar por nombre o email..." oninput="buscarCliente(this.value)"
                        class="flex-1 px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <button onclick="limpiarCliente()" class="px-4 py-2.5 bg-slate-100 text-slate-500 rounded-xl text-sm font-semibold hover:bg-slate-200 transition">Mostrador</button>
                </div>
                <!-- Caja de resultados -->
                <div id="cliente-results" class="hidden absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50"></div>

                <!-- Cliente seleccionado -->
                <div id="cliente-sel" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700 flex items-center justify-between">
                    <span id="cliente-sel-nombre" class="font-semibold"></span>
                    <button onclick="limpiarCliente()" class="text-blue-400 hover:text-blue-600"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <!-- Método de pago -->
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Método de pago *</label>
                <select id="sel-metodo" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia bancaria">Transferencia bancaria</option>
                </select>
            </div>

            <!-- Agregar Producto -->
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Agregar producto al detalle</label>
                <div class="flex flex-wrap gap-2">
                    <select id="sel-producto" class="flex-1 min-w-[200px] px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        <option value="">Seleccionar producto...</option>
                        @foreach ($productos as $p)
                        @if ($p->estado === 'Activo' && $p->stock > 0)
                        <option value="{{ $p->id }}" data-precio="{{ $p->precio_venta }}" data-stock="{{ $p->stock }}">
                            {{ $p->nombre }} ({{ $p->talla }}/{{ $p->color }}) — Stock: {{ $p->stock }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                    <input type="number" id="inp-qty" placeholder="Cant." min="1"
                        class="w-20 px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                    <button onclick="agregarItem()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-md transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Detalle de la Venta (Carrito) -->
            <div id="items-list" class="space-y-2 border-t border-slate-100 pt-3">
                <!-- Dibujado por JavaScript -->
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                <span class="font-semibold text-slate-700">Total Venta</span>
                <span id="total-display" class="text-xl font-bold text-blue-600">$0.00</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="guardarVenta()" class="flex-1 py-3 bg-brand-accent text-white font-semibold rounded-xl transition text-sm hover:shadow-lg" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                    <i class="fas fa-check mr-2 text-xs"></i>Confirmar venta
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- MODAL VER DETALLE VENTA -->
<div id="modal-det" class="modal-overlay hidden">
    <div class="modal-det scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-800">Detalle de Venta</h3>
            <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="det-body" class="p-6 overflow-y-auto flex-1">
            <!-- Dibujado por FETCH AJAX -->
        </div>
    </div>
</div>

<!-- MODAL SOLICITAR DEVOLUCION -->
<div id="modal-devolucion" class="modal-overlay hidden">
    <div class="modal-box scale-up" style="max-width: 560px;">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-800">Solicitar Devolución</h3>
            <button onclick="closeModalDevolucion()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 space-y-4">
            <p class="text-sm text-slate-600">Registrar devolución para la venta <strong id="dev-venta-id-txt"></strong>.</p>

            <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Productos de la Venta</h4>
            <div class="overflow-x-auto mb-4 border border-slate-100 rounded-xl">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left py-2.5 px-3 text-slate-500 uppercase">Producto</th>
                            <th class="text-center py-2.5 px-3 text-slate-500 uppercase">Comprado</th>
                            <th class="text-center py-2.5 px-3 text-slate-500 uppercase">A Devolver</th>
                        </tr>
                    </thead>
                    <tbody id="dev-productos-body">
                        <!-- Cargado por JS -->
                    </tbody>
                </table>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Motivo de Devolución *</label>
                <textarea id="dev-motivo" rows="2" placeholder="Explique el motivo de la devolución..."
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-brand-accent resize-none transition"></textarea>
            </div>

            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button onclick="closeModalDevolucion()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="submitDevolucion()" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition text-sm hover:shadow-lg">
                    <i class="fas fa-undo mr-2 text-xs"></i>Confirmar Devolución
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- SweetAlert2 para notificaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const STORE_URL = "{{ route('ventas.store') }}";
    const BASE_URL = "{{ url('/ventas') }}";
    const CLIENTES_SEARCH_URL = "{{ route('ventas.buscar-cliente') }}";
    const DEVOLUCIONES_STORE_URL = "{{ route('devoluciones.store') }}";

    let devVentaId = null;

    function openDevolucion(id) {
        devVentaId = id;
        document.getElementById('dev-venta-id-txt').textContent = '#' + id;
        document.getElementById('dev-motivo').value = '';

        const tbody = document.getElementById('dev-productos-body');
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando productos...</td></tr>';
        document.getElementById('modal-devolucion').classList.remove('hidden');

        fetch(`${BASE_URL}/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(detalles => {
                if (!detalles || !detalles.length) {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-red-500">No se pudieron cargar los productos</td></tr>';
                    return;
                }
                tbody.innerHTML = detalles.map(i => `
                <tr class="border-b border-slate-50 last:border-b-0 py-2 trow text-slate-700">
                    <td class="py-2.5 px-3 text-left font-medium text-slate-700">${i.producto} <span class="text-slate-400">(${i.talla}/${i.color})</span></td>
                    <td class="text-center py-2.5 px-3 text-slate-600">${i.cantidad}</td>
                    <td class="text-center py-2.5 px-3">
                        <input type="number" min="0" max="${i.cantidad}" value="0" 
                            data-id="${i.id_producto}" data-precio="${i.precio_unitario}"
                            class="w-16 px-2 py-1 border rounded-lg text-center dev-input-cant focus:outline-none focus:border-brand-accent">
                    </td>
                </tr>
            `).join('');
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-red-500">Error al cargar productos</td></tr>';
            });
    }

    function closeModalDevolucion() {
        document.getElementById('modal-devolucion').classList.add('hidden');
        devVentaId = null;
    }

    function submitDevolucion() {
        const motivo = document.getElementById('dev-motivo').value.trim();
        if (!motivo) {
            showToast('Ingresa el motivo de la devolución', 'error');
            return;
        }

        const devItems = [];
        document.querySelectorAll('.dev-input-cant').forEach(input => {
            const cant = parseInt(input.value) || 0;
            if (cant > 0) {
                devItems.push({
                    id_producto: parseInt(input.dataset.id),
                    cantidad: cant,
                    precio_unitario: parseFloat(input.dataset.precio)
                });
            }
        });

        if (devItems.length === 0) {
            showToast('Selecciona al menos un producto con cantidad mayor a cero', 'error');
            return;
        }

        const fd = new FormData();
        fd.append('venta_id', devVentaId);
        fd.append('motivo', motivo);
        fd.append('items', JSON.stringify(devItems));
        fd.append('_token', '{{ csrf_token() }}');

        fetch(DEVOLUCIONES_STORE_URL, {
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
                    closeModalDevolucion();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Solicitud Registrada!',
                        text: d.msg,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Entendido'
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
                    text: err.message.substring(0, 200) || 'Ocurrió un error inesperado al procesar la devolución.'
                });
            });
    }

    function closeModalDevolucion() {
        document.getElementById('modal-devolucion').classList.add('hidden');
        devVentaId = null;
    }


    let items = [];
    let clienteId = null;
    let clienteTimer;

    // ── Advertencia de caja cerrada ────────────────────────────────────────────────
    function mostrarAdvertenciaCaja() {
        Swal.fire({
            icon: 'warning',
            title: 'Caja Cerrada',
            text: 'Debes abrir una caja en el módulo de Caja antes de poder registrar ventas.',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Entendido'
        });
    }

    // ── Paginación de Ventas ──────────────────────────────────────────────────────
    let currentSalesPage = 1;
    const salesRecordsPerPage = 10;

    document.addEventListener('DOMContentLoaded', () => {
        applySalesPagination();
    });

    function applySalesPagination() {
        const rows = Array.from(document.querySelectorAll('#table-body tr[id^="vrow-"]')).filter(row => row.style.display !== 'none');
        const infoSpan = document.getElementById('sales-page-info');
        const prevBtn = document.getElementById('btn-sales-prev');
        const nextBtn = document.getElementById('btn-sales-next');

        const total = rows.length;
        const totalPages = Math.ceil(total / salesRecordsPerPage) || 1;

        if (currentSalesPage > totalPages) {
            currentSalesPage = totalPages;
        }

        const startIdx = (currentSalesPage - 1) * salesRecordsPerPage;
        const endIdx = startIdx + salesRecordsPerPage;

        rows.forEach((row, idx) => {
            if (idx >= startIdx && idx < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (total === 0) {
            infoSpan.textContent = "Mostrando registros 0 de 0";
            prevBtn.disabled = true;
            nextBtn.disabled = true;
        } else {
            infoSpan.textContent = `Mostrando registros ${startIdx + 1}-${Math.min(endIdx, total)} de ${total}`;
            prevBtn.disabled = currentSalesPage === 1;
            nextBtn.disabled = currentSalesPage === totalPages;
        }
    }

    function prevSalesPage() {
        if (currentSalesPage > 1) {
            currentSalesPage--;
            applySalesPagination();
        }
    }

    function nextSalesPage() {
        currentSalesPage++;
        applySalesPagination();
    }

    // ── Filtrado local reactivo de ventas ──────────────────────────────────────────
    function filterTable() {
        const desde = document.getElementById('filter-desde').value;
        const hasta = document.getElementById('filter-hasta').value;
        const metodo = document.getElementById('filter-metodo').value;
        const estado = document.getElementById('filter-estado').value;

        let total = 0;
        let filtrados = 0;

        document.querySelectorAll('#table-body tr[id^="vrow-"]').forEach(row => {
            total++;
            const fecha = row.dataset.fecha;

            const show = (!metodo || row.dataset.metodo === metodo) &&
                (!estado || row.dataset.estado === estado) &&
                (!desde || fecha >= desde) &&
                (!hasta || fecha <= hasta);

            row.style.display = show ? '' : 'none';
            if (show) filtrados++;
        });

        document.getElementById('cant-filtrada').textContent = filtrados;
        document.getElementById('empty-state').classList.toggle('hidden', filtrados > 0);
        
        currentSalesPage = 1;
        applySalesPagination();
    }

    // ── Apertura y Cierre de Modales ────────────────────────────────────────────────
    @if($cajaAbierta)

    function openModal() {
        items = [];
        clienteId = null;
        renderItems();
        document.getElementById('inp-cliente').value = '';
        document.getElementById('cliente-results').classList.add('hidden');
        document.getElementById('cliente-sel').classList.add('hidden');
        document.getElementById('modal-venta').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-venta').classList.add('hidden');
    }
    @endif

    function closeDet() {
        document.getElementById('modal-det').classList.add('hidden');
    }

    // Cerrar modales con escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            @if($cajaAbierta) closeModal();
            @endif
            closeDet();
        }
    });

    // ── Buscar y autocompletar Clientes ─────────────────────────────────────────────
    @if($cajaAbierta)

    function buscarCliente(q) {
        clearTimeout(clienteTimer);
        if (q.trim().length < 2) {
            document.getElementById('cliente-results').classList.add('hidden');
            return;
        }

        clienteTimer = setTimeout(() => {
            fetch(`${CLIENTES_SEARCH_URL}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('cliente-results');
                    if (!data.length) {
                        box.classList.add('hidden');
                        return;
                    }
                    box.innerHTML = data.map(c => `
                    <div class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer text-sm text-slate-700 border-b border-slate-100 last:border-0"
                        onclick="selCliente(${c.id}, '${c.nombre} ${c.apellido}')">
                        ${c.nombre} ${c.apellido} <span class="text-slate-400 text-xs">(${c.email})</span>
                    </div>
                `).join('');
                    box.classList.remove('hidden');
                })
                .catch(() => {
                    document.getElementById('cliente-results').classList.add('hidden');
                });
        }, 300);
    }

    function selCliente(id, nombreCompleto) {
        clienteId = id;
        document.getElementById('inp-cliente').value = '';
        document.getElementById('cliente-results').classList.add('hidden');
        document.getElementById('cliente-sel-nombre').textContent = nombreCompleto;
        document.getElementById('cliente-sel').classList.remove('hidden');
    }

    function limpiarCliente() {
        clienteId = null;
        document.getElementById('inp-cliente').value = '';
        document.getElementById('cliente-sel').classList.add('hidden');
        document.getElementById('cliente-results').classList.add('hidden');
    }

    // ── Lógica del Carrito local de Ventas ─────────────────────────────────────────
    function agregarItem() {
        const sel = document.getElementById('sel-producto');
        const id = parseInt(sel.value);
        const qty = parseInt(document.getElementById('inp-qty').value);
        const opt = sel.options[sel.selectedIndex];

        if (!id || !qty || qty < 1) {
            showToast('Selecciona un producto y una cantidad válida.', 'error');
            return;
        }

        const precio = parseFloat(opt.dataset.precio || 0);
        const stockMax = parseInt(opt.dataset.stock || 0);
        const nombre = opt.text.trim();

        // Validar stock localmente en el carrito
        const idx = items.findIndex(i => i.id_producto === id);
        const totalQty = (idx >= 0 ? items[idx].cantidad : 0) + qty;

        if (totalQty > stockMax) {
            showToast(`Stock insuficiente. Disponible en stock: ${stockMax}`, 'error');
            return;
        }

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
        document.getElementById('inp-qty').value = '';
        document.getElementById('sel-producto').value = '';
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

    // ── Guardar Venta (Fetch AJAX) ───────────────────────────────────────────────────
    function guardarVenta() {
        if (!items.length) {
            showToast('Agrega al menos un producto a la venta.', 'error');
            return;
        }

        const metodo = document.getElementById('sel-metodo').value;

        const fd = new FormData();
        fd.append('cliente_id', clienteId || '');
        fd.append('metodo_pago', metodo);
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
                        title: '¡Venta Registrada!',
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
                    text: err.message.substring(0, 200) || 'Ocurrió un error inesperado al procesar la venta.'
                });
            });
    }
    @endif

    // ── Ver Detalles de Venta (Fetch AJAX) ─────────────────────────────────────────────
    function verDetalle(id) {
        fetch(`${BASE_URL}/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(detalles => {
                const body = document.getElementById('det-body');

                // Obtener datos del row para la cabecera del modal
                const row = document.getElementById(`vrow-${id}`);
                const fecha = row.cells[1].textContent;
                const cliente = row.cells[2].textContent;
                const empleado = row.cells[3].textContent;
                const metodo = row.cells[4].textContent.trim();
                const total = row.cells[5].textContent;

                body.innerHTML = `
                <div class="grid grid-cols-2 gap-3 mb-5 text-sm p-4 bg-slate-50 rounded-2xl">
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Fecha</p><p class="font-semibold text-slate-800">${fecha}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Cliente</p><p class="font-semibold text-slate-800">${cliente}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Empleado</p><p class="font-semibold text-slate-800">${empleado}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Método</p><p class="font-semibold text-slate-800">${metodo}</p></div>
                </div>
                <table class="w-full text-sm mb-4 whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">Cant.</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">P.Unit</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detalles.map(i => `
                            <tr class="border-t border-slate-50 trow">
                                <td class="py-3 font-medium text-slate-800">${i.producto} <span class="text-slate-400 text-xs">(${i.talla}/${i.color})</span></td>
                                <td class="py-3 text-right text-slate-700">${i.cantidad}</td>
                                <td class="py-3 text-right text-slate-700">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                                <td class="py-3 text-right font-bold text-blue-700">$${parseFloat(i.subtotal).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
                    <span class="font-semibold text-slate-700">Total Venta</span>
                    <span class="text-xl font-bold text-blue-600">${total}</span>
                </div>
            `;
                document.getElementById('modal-det').classList.remove('hidden');
            })
            .catch(err => {
                showToast('Error al obtener los detalles de la venta.', 'error');
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