@extends('layouts.Sidebarempleado')

@section('titulo', 'Abastecimiento (Empleado)')

@section('content')
<div class="p-6 md:p-8 font-sans">
    <!-- Cabecera -->
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                <i class="fas fa-truck text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Abastecimiento</h1>
                <p class="text-brand-muted text-xs font-light">Módulo de compras y registro de stock entrante</p>
            </div>
        </div>
        <button onclick="openModal()" 
            class="flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:-translate-y-0.5 transition-all" 
            style="box-shadow: 0 4px 12px rgba(59,130,246,.25);">
            <i class="fas fa-plus text-xs"></i> <span>Registrar compra</span>
        </button>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['total'] }}</p>
                    <p class="text-xs text-slate-500">Total transacciones</p>
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
                    <p class="text-xs text-slate-500">Monto invertido</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $kpi['hoy'] }}</p>
                    <p class="text-xs text-slate-500">Ingresos hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['#', 'Fecha', 'Proveedor', 'Usuario', 'Total', 'Acciones'] as $h)
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" id="compras-tbody">
                    @forelse ($compras as $c)
                        <tr class="trow cursor-pointer text-slate-700" id="crow-{{ $c->id }}" onclick="verDetalle({{ $c->id }})">
                            <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#{{ $c->id }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $c->fecha ? $c->fecha->format('d/m/Y H:i') : '—' }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $c->proveedor->nombre ?? '—' }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-600">{{ $c->usuario->nombre ?? '—' }} {{ $c->usuario->apellido ?? '' }}</td>
                            <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">${{ number_format($c->total, 2) }}</td>
                            <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                <button onclick="verDetalle({{ $c->id }})" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition" title="Ver detalle">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
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
    </div>
</div>

<!-- Modal Registrar Compra -->
<div id="modal-compra" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-[2rem] shadow-2xl flex flex-col max-h-[94vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Registrar abastecimiento</h3>
                    <p class="text-xs text-slate-500">Agrega productos comprados al proveedor y actualiza el stock</p>
                </div>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="form-compra" method="POST" action="{{ route('compras.store') }}" onsubmit="prevenirEnvio(event)" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Formulario -->
                <div class="md:col-span-5 space-y-4">
                    <!-- Proveedor -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Proveedor *</label>
                        <select name="proveedor_id" id="proveedor-id" class="w-full py-2.5 px-4 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none focus:border-brand-accent transition">
                            <option value="">Seleccionar proveedor...</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Producto -->
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Prenda comprada</label>
                        <div class="flex gap-2">
                            <select id="sel-producto" class="flex-1 min-w-[150px] px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                                <option value="">Seleccionar...</option>
                                @foreach ($productos as $p)
                                    <option value="{{ $p->id }}" data-precio="{{ $p->precio_compra }}">
                                        {{ $p->nombre }} ({{ $p->talla }}/{{ $p->color }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cantidad y Costo -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Cant. *</label>
                            <input type="number" id="inp-qty" placeholder="0" min="1"
                                class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Costo unit. *</label>
                            <input type="number" id="inp-cost" placeholder="$ 0.00" min="0" step="0.01"
                                class="w-full px-3 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
                        </div>
                    </div>

                    <button type="button" onclick="agregarItem()" class="w-full py-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-semibold rounded-xl transition text-sm">
                        <i class="fas fa-plus mr-2 text-xs"></i>Agregar al detalle
                    </button>
                </div>

                <!-- Detalle -->
                <div class="md:col-span-7 flex flex-col h-full min-h-[300px]">
                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 tracking-wider">Productos a ingresar</h4>
                    <input type="hidden" name="items" id="items-json">
                    <div class="flex-1 border border-slate-100 rounded-2xl p-4 overflow-y-auto bg-slate-50/50 max-h-[250px]" id="cart-container">
                        <div class="flex flex-col items-center justify-center h-full text-slate-400 py-10" id="cart-empty">
                            <i class="fas fa-box text-3xl mb-2 opacity-30"></i>
                            <p class="text-xs">El detalle está vacío</p>
                        </div>
                        <div class="space-y-2 hidden" id="cart-list"></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">Total Compra</span>
                        <span class="text-2xl font-bold text-indigo-600" id="cart-total">$0.00</span>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-[2rem]">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-100 transition text-sm">Cancelar</button>
                <button type="button" onclick="enviarCompra()" class="px-5 py-2.5 bg-brand-accent text-white font-semibold rounded-xl hover:shadow-lg transition text-sm">Procesar Abastecimiento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detalle Compra -->
<div id="modal-det" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Detalle de Compra</h3>
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
    const BASE_URL = "{{ url('/compras') }}";
    
    let items = [];

    function openModal() {
        items = [];
        document.getElementById('proveedor-id').value = '';
        document.getElementById('sel-producto').value = '';
        document.getElementById('inp-qty').value = '';
        document.getElementById('inp-cost').value = '';
        updateCartUI();
        document.getElementById('modal-compra').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-compra').classList.add('hidden');
    }

    function prevenirEnvio(e) {
        e.preventDefault();
    }

    // Lógica para añadir producto al selector
    document.getElementById('sel-producto').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const cost = opt.dataset.precio;
        if (cost) {
            document.getElementById('inp-cost').value = cost;
        }
    });

    function agregarItem() {
        const sel = document.getElementById('sel-producto');
        const qtyInp = document.getElementById('inp-qty');
        const costInp = document.getElementById('inp-cost');
        
        const id = parseInt(sel.value);
        const qty = parseInt(qtyInp.value);
        const cost = parseFloat(costInp.value);

        if (!id || !qty || qty < 1 || isNaN(cost) || cost < 0) {
            Swal.fire('Atención', 'Selecciona una prenda, cantidad y costo válido.', 'warning');
            return;
        }

        const opt = sel.options[sel.selectedIndex];
        
        // Comprobar si ya existe
        const exist = items.find(i => i.id_producto === id);
        if (exist) {
            exist.cantidad += qty;
            exist.precio_unitario = cost; // actualizar con el último costo
        } else {
            items.push({
                id_producto: id,
                nombre: opt.text.trim(),
                cantidad: qty,
                precio_unitario: cost
            });
        }

        sel.value = '';
        qtyInp.value = '';
        costInp.value = '';
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
                    <button type="button" onclick="removeItem(${idx})" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `).join('');

        const total = items.reduce((s, i) => s + i.cantidad * i.precio_unitario, 0);
        totalEl.textContent = '$' + total.toFixed(2);
    }

    function enviarCompra() {
        const provId = document.getElementById('proveedor-id').value;
        if (!provId) {
            Swal.fire('Atención', 'Selecciona un proveedor.', 'warning');
            return;
        }
        if (!items.length) {
            Swal.fire('Atención', 'Agrega al menos una prenda al detalle.', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('proveedor_id', provId);
        fd.append('items', JSON.stringify(items));
        fd.append('_token', '{{ csrf_token() }}');

        fetch(BASE_URL, {
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
                    title: '¡Compra Registrada!',
                    text: d.msg,
                    confirmButtonColor: '#2563eb'
                }).then(() => location.reload());
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Ocurrió un error al procesar el abastecimiento.', 'error');
        });
    }

    function verDetalle(id) {
        fetch(`${BASE_URL}/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(detalles => {
            const body = document.getElementById('det-body');
            const row = document.getElementById(`crow-${id}`);
            const fecha = row.cells[1].textContent;
            const proveedor = row.cells[2].textContent;
            const empleado = row.cells[3].textContent;
            const total = row.cells[4].textContent;

            body.innerHTML = `
                <div class="grid grid-cols-2 gap-3 mb-5 text-sm p-4 bg-slate-50 rounded-2xl">
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Fecha</p><p class="font-semibold text-slate-800">${fecha}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Proveedor</p><p class="font-semibold text-slate-800">${proveedor}</p></div>
                    <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Comprador</p><p class="font-semibold text-slate-800">${empleado}</p></div>
                </div>
                <table class="w-full text-sm mb-5">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-500 text-xs uppercase font-bold">
                            <th class="text-left py-2">Producto</th>
                            <th class="text-center py-2">Cant.</th>
                            <th class="text-right py-2">Costo</th>
                            <th class="text-right py-2">Subtotal</th>
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
                    <span class="font-semibold text-slate-700">Total Inversión</span>
                    <span class="text-xl font-bold text-indigo-600">${total}</span>
                </div>
            `;
            document.getElementById('modal-det').classList.remove('hidden');
        });
    }

    function closeDet() {
        document.getElementById('modal-det').classList.add('hidden');
    }
</script>
@endpush
@endsection
