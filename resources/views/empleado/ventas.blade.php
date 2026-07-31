@extends('layouts.Sidebarempleado')

@section('titulo', 'Ventas (Empleado)')

@section('content')
<div class="p-6 md:p-8 font-sans">
    <!-- Cabecera -->
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-shopping-cart text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Ventas</h1>
                <p class="text-brand-muted text-xs font-light">Panel operativo de ventas</p>
            </div>
            @if (!$cajaAbierta)
                <span class="ml-2 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Sin caja abierta
                </span>
            @endif
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

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Total ventas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($kpi['monto'], 2) }}</p>
                    <p class="text-xs text-slate-500">Ingresos totales</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['completadas'] }}</p>
                    <p class="text-xs text-slate-500">Completadas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['hoy'] }}</p>
                    <p class="text-xs text-slate-500">Ventas hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-5 mb-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Fecha desde</label>
                <input type="date" id="filter-desde" onchange="filterTable()" 
                    class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Fecha hasta</label>
                <input type="date" id="filter-hasta" onchange="filterTable()" 
                    class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Método de pago</label>
                <select id="filter-metodo" onchange="filterTable()" 
                    class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                    <option value="">Todos los métodos</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia bancaria">Transferencia</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">Estado</label>
                <select id="filter-estado" onchange="filterTable()" 
                    class="w-full py-2 px-3 bg-slate-55 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                    <option value="">Todos los estados</option>
                    <option value="Completada">Completada</option>
                    <option value="Devuelta">Devuelta</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['#', 'Fecha', 'Cliente', 'Empleado', 'Método', 'Total', 'Estado', 'Acciones'] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="ventas-tbody">
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
        </div>
    </div>
</div>

<!-- Modal Nueva Venta -->
@if ($cajaAbierta)
<div id="modal-venta" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-[2rem] shadow-2xl flex flex-col max-h-[94vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-brand-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Registrar nueva venta</h3>
                    <p class="text-xs text-slate-500">Agrega productos al carrito y selecciona el método de pago</p>
                </div>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Izquierda: Formulario -->
            <div class="md:col-span-5 space-y-4">
                <!-- Cliente -->
                <div class="relative">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Cliente</label>
                    <div class="relative">
                        <input type="text" id="cliente-input" placeholder="Buscar cliente por nombre o email..." oninput="buscarCliente(this.value)"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-brand-accent transition">
                        <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                    </div>
                    <div id="cliente-results" class="hidden absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50"></div>
                    <div id="cliente-sel" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700 flex items-center justify-between">
                        <span id="cliente-sel-nombre" class="font-semibold"></span>
                        <button onclick="removerCliente()" class="text-blue-500 hover:text-blue-700"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Método de pago *</label>
                    <select id="metodo-pago" class="w-full py-2.5 px-4 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                    </select>
                </div>

                <!-- Añadir producto -->
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Agregar prenda</label>
                    <div class="flex gap-2">
                        <select id="sel-producto" class="flex-1 min-w-[150px] px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                            <option value="">Seleccionar...</option>
                            @foreach ($productos as $p)
                                @if ($p->estado === 'Activo' && $p->stock > 0)
                                    <option value="{{ $p->id }}" data-precio="{{ $p->precio_view ?? $p->precio_venta }}" data-stock="{{ $p->stock }}">
                                        {{ $p->nombre }} ({{ $p->talla }}/{{ $p->color }}) — Stock: {{ $p->stock }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <input type="number" id="inp-qty" placeholder="Cant." min="1" 
                            class="w-16 px-2 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 text-center focus:outline-none transition">
                        <button onclick="agregarItem()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-md transition">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Derecha: Detalle de Venta -->
            <div class="md:col-span-7 flex flex-col h-full min-h-[300px]">
                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 tracking-wider">Prendas en el carrito</h4>
                <div class="flex-1 border border-slate-100 rounded-2xl p-4 overflow-y-auto bg-slate-50/50 max-h-[250px]" id="cart-container">
                    <div class="flex flex-col items-center justify-center h-full text-slate-400 py-10" id="cart-empty">
                        <i class="fas fa-shopping-bag text-3xl mb-2 opacity-30"></i>
                        <p class="text-xs">El carrito está vacío</p>
                    </div>
                    <div class="space-y-2 hidden" id="cart-list"></div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-700">Total a pagar</span>
                    <span class="text-2xl font-bold text-blue-600" id="cart-total">$0.00</span>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-[2rem]">
            <button onclick="closeModal()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition text-sm">Cancelar</button>
            <button onclick="registrarVenta()" class="px-5 py-2.5 bg-brand-accent text-white font-semibold rounded-xl hover:shadow-lg transition text-sm">Procesar Venta</button>
        </div>
    </div>
</div>
@endif

<!-- Modal Detalle Venta -->
<div id="modal-det" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-brand-accent rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Detalle de Venta</h3>
            </div>
            <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="det-body" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const BASE_URL = "{{ url('/ventas') }}";
    const CLIENTES_SEARCH_URL = "{{ route('ventas.buscar-cliente') }}";
    const STORE_URL = "{{ route('ventas.store') }}";
    
    function mostrarAdvertenciaCaja() {
        Swal.fire({
            icon: 'warning',
            title: 'Caja cerrada',
            text: 'Debes abrir la caja primero para poder registrar ventas.',
            confirmButtonColor: '#2563eb',
        });
    }

    let items = [];
    let clienteId = null;
    let clienteTimer;

    function filterTable() {
        const desde = document.getElementById('filter-desde').value;
        const hasta = document.getElementById('filter-hasta').value;
        const metodo = document.getElementById('filter-metodo').value;
        const estado = document.getElementById('filter-estado').value;
        
        const rows = document.querySelectorAll('#ventas-tbody tr[id^="vrow-"]');
        rows.forEach(row => {
            const fecha = row.dataset.fecha;
            const show = (!metodo || row.dataset.metodo === metodo) &&
                (!estado || row.dataset.estado === estado) &&
                (!desde || fecha >= desde) &&
                (!hasta || fecha <= hasta);
            row.style.display = show ? '' : 'none';
        });
    }

    @if ($cajaAbierta)
    function openModal() {
        items = [];
        clienteId = null;
        document.getElementById('cliente-input').value = '';
        document.getElementById('cliente-sel').classList.add('hidden');
        document.getElementById('cliente-results').classList.add('hidden');
        document.getElementById('sel-producto').value = '';
        document.getElementById('inp-qty').value = '';
        updateCartUI();
        document.getElementById('modal-venta').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('modal-venta').classList.add('hidden');
    }

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
                    if (!data.length) { box.classList.add('hidden'); return; }
                    box.innerHTML = data.map(c => `
                        <div class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm text-slate-700"
                            onclick="selCliente(${c.id}, '${c.nombre} ${c.apellido}')">
                            ${c.nombre} ${c.apellido} <span class="text-slate-400 text-xs">(${c.email})</span>
                        </div>
                    `).join('');
                    box.classList.remove('hidden');
                });
        }, 300);
    }

    function selCliente(id, name) {
        clienteId = id;
        document.getElementById('cliente-sel-nombre').textContent = name;
        document.getElementById('cliente-sel').classList.remove('hidden');
        document.getElementById('cliente-results').classList.add('hidden');
        document.getElementById('cliente-input').value = '';
    }

    function removerCliente() {
        clienteId = null;
        document.getElementById('cliente-sel').classList.add('hidden');
    }

    function agregarItem() {
        const sel = document.getElementById('sel-producto');
        const qtyInp = document.getElementById('inp-qty');
        const id = parseInt(sel.value);
        const qty = parseInt(qtyInp.value);
        if (!id || !qty || qty < 1) {
            showToast('Selecciona un producto y cantidad válida.', 'error');
            return;
        }
        const opt = sel.options[sel.selectedIndex];
        const stock = parseInt(opt.dataset.stock);
        const precio = parseFloat(opt.dataset.precio);
        
        // Buscar si ya está
        const exist = items.find(i => i.id_producto === id);
        const currentQty = exist ? exist.cantidad : 0;
        if (currentQty + qty > stock) {
            showToast(`Stock insuficiente. Solo quedan ${stock} unidades.`, 'error');
            return;
        }

        if (exist) {
            exist.cantidad += qty;
        } else {
            items.push({
                id_producto: id,
                nombre: opt.text.split(' — ')[0],
                cantidad: qty,
                precio_unitario: precio
            });
        }
        sel.value = '';
        qtyInp.value = '';
        updateCartUI();
    }

    function removeItem(idx) {
        items.splice(idx, 1);
        updateCartUI();
    }

    function updateCartUI() {
        const empty = document.getElementById('cart-empty');
        const list = document.getElementById('cart-list');
        const totalEl = document.getElementById('cart-total');
        if (items.length === 0) {
            empty.classList.remove('hidden');
            list.classList.add('hidden');
            totalEl.textContent = '$0.00';
            return;
        }
        empty.classList.add('hidden');
        list.classList.remove('hidden');
        list.innerHTML = items.map((it, idx) => `
            <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-xl text-sm">
                <div>
                    <p class="font-medium text-slate-800">${it.nombre}</p>
                    <p class="text-xs text-slate-400">${it.cantidad} × $${it.precio_unitario.toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-slate-800">$${(it.cantidad * it.precio_unitario).toFixed(2)}</span>
                    <button onclick="removeItem(${idx})" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `).join('');
        const total = items.reduce((s, i) => s + i.cantidad * i.precio_unitario, 0);
        totalEl.textContent = '$' + total.toFixed(2);
    }

    function registrarVenta() {
        if (!items.length) {
            showToast('El carrito está vacío.', 'error');
            return;
        }
        const fd = new FormData();
        if (clienteId) fd.append('cliente_id', clienteId);
        fd.append('metodo_pago', document.getElementById('metodo-pago').value);
        fd.append('items', JSON.stringify(items));
        fd.append('_token', '{{ csrf_token() }}');

        fetch(STORE_URL, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: '¡Venta Registrada!',
                    text: d.msg,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Excelente'
                }).then(() => location.reload());
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Ocurrió un error al registrar la venta.', 'error');
        });
    }
    @endif

    function verDetalle(id) {
        fetch(`${BASE_URL}/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(detalles => {
            const body = document.getElementById('det-body');
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
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Vendedor</p><p class="font-semibold text-slate-800">${empleado}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Método de pago</p><p class="font-semibold text-slate-800">${metodo}</p></div>
                </div>
                <table class="w-full text-sm mb-5">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-550 text-xs uppercase">
                            <th class="text-left py-2 font-bold">Producto</th>
                            <th class="text-center py-2 font-bold">Cant.</th>
                            <th class="text-right py-2 font-bold">Precio</th>
                            <th class="text-right py-2 font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${detalles.map(i => `
                            <tr class="border-b border-slate-50 last:border-b-0 text-slate-700">
                                <td class="py-2.5 font-medium">${i.producto} <span class="text-slate-400 text-xs">(${i.talla}/${i.color})</span></td>
                                <td class="py-2.5 text-center text-slate-600">${i.cantidad}</td>
                                <td class="py-2.5 text-right text-slate-600">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                                <td class="py-2.5 text-right font-bold text-slate-800">$${(i.cantidad * parseFloat(i.precio_unitario)).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <span class="font-semibold text-slate-700">Total Venta</span>
                    <span class="text-xl font-bold text-blue-600">${total}</span>
                </div>
            `;
            document.getElementById('modal-det').classList.remove('hidden');
        });
    }

    function closeDet() {
        document.getElementById('modal-det').classList.add('hidden');
    }

    function showToast(msg, type = 'success') {
        Swal.fire({
            icon: type,
            title: msg,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });
    }
</script>
@endpush
@endsection
