@extends('layouts.Sidebarcliente')

@section('titulo', 'Mis Compras')

@section('content')
<div class="p-6 md:p-8 font-sans">
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark mb-1">Mis Compras</h2>
            <p class="text-brand-muted font-light text-sm">Historial completo de pedidos realizados</p>
        </div>
        <div class="text-sm font-semibold text-blue-600 bg-blue-50 px-4 py-2 rounded-xl">
            Total invertido: ${{ number_format($totalGastado, 2) }}
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Listado de Pedidos</h3>
            <p class="text-xs text-slate-500 mt-0.5">Haz clic sobre cualquier fila para solicitar una devolución o ver el detalle</p>
        </div>

        @if ($misVentas->isEmpty())
            <div class="py-20 text-center text-slate-400">
                <i class="fas fa-shopping-bag text-5xl mb-4 block opacity-20"></i>
                <p class="text-lg font-semibold text-slate-600">Aún no tienes compras</p>
                <p class="text-sm mt-1">Registra tu primera compra en nuestra boutique.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr>
                            @foreach (['#', 'Fecha', 'Método', 'Total', 'Estado', 'Acciones'] as $h)
                                <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($misVentas as $v)
                            <tr class="trow hover:bg-slate-50 transition-colors cursor-pointer text-slate-700" onclick="verDetalle({{ $v->id }})">
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm">{{ $v->fecha ? $v->fecha->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm">
                                    <span class="badge {{ $v->metodo_pago === 'Efectivo' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $v->metodo_pago }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-slate-800">${{ number_format($v->total, 2) }}</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="badge {{ $v->estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $v->estado }}</span>
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50" onclick="event.stopPropagation()">
                                    <button onclick="verDetalle({{ $v->id }})" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition" title="Ver detalle">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- MODAL DETALLE / DEVOLUCIÓN -->
<div id="modal-det" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 id="modal-title" class="text-lg font-bold text-slate-800">Detalle de compra</h3>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="modal-body" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<!-- MODAL CONSTANCIA DE DEVOLUCIÓN -->
<div id="modal-constancia" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Devolución registrada</h3>
                    <p class="text-xs text-slate-500">Constancia de solicitud</p>
                </div>
            </div>
            <button onclick="closeConstancia()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="constancia-body" class="p-6 overflow-y-auto flex-1"></div>
        <div class="p-6 border-t border-slate-100 flex gap-3">
            <button onclick="window.print()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">
                <i class="fas fa-print mr-2 text-xs"></i>Imprimir
            </button>
            <button onclick="closeConstanciaAndReload()" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-sm hover:shadow-lg">
                <i class="fas fa-check mr-2 text-xs"></i>Listo
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const BASE_URL = "{{ url('/ventas') }}";
    const DEVOLUCIONES_STORE_URL = "{{ route('devoluciones.store') }}";
    let ventaActual = null;
    let itemsDev = [];

    function verDetalle(id) {
        fetch(`${BASE_URL}/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(detalles => {
            ventaActual = detalles;
            // Estructura de items para devolución
            itemsDev = detalles.map(d => ({
                id_producto: d.id_producto,
                producto: d.producto,
                talla: d.talla,
                color: d.color,
                cantidad: d.cantidad,
                precio_unitario: d.precio_unitario,
                dev_qty: 0
            }));
            renderDetalle(id);
            document.getElementById('modal-det').classList.remove('hidden');
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudo cargar el detalle del pedido', 'error');
        });
    }

    function renderDetalle(id) {
        // Encontrar row para los metadatos
        const row = document.querySelector(`tr[onclick="verDetalle(${id})"]`);
        const fecha = row.cells[1].textContent;
        const metodo = row.cells[2].textContent.trim();
        const total = row.cells[3].textContent;
        const estado = row.cells[4].textContent.trim();
        const esCompletada = estado === 'Completada';

        document.getElementById('modal-title').textContent = `Compra #${String(id).padStart(5, '0')}`;
        document.getElementById('modal-body').innerHTML = `
            <div class="grid grid-cols-2 gap-3 mb-5 text-sm p-4 bg-slate-50 rounded-2xl">
                <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Fecha</p><p class="font-semibold text-slate-800">${fecha}</p></div>
                <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Método de pago</p><p class="font-semibold text-slate-800">${metodo}</p></div>
                <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Estado</p>
                    <span class="badge ${estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">${estado}</span>
                </div>
                <div><p class="text-xs text-slate-400 font-bold uppercase mb-0.5">Total</p><p class="font-bold text-blue-600 text-lg">${total}</p></div>
            </div>
            <table class="w-full text-sm mb-5">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-500 text-xs uppercase">
                        <th class="text-left py-2">Producto</th>
                        <th class="text-center py-2">Cant.</th>
                        <th class="text-right py-2">Precio</th>
                        <th class="text-right py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${ventaActual.map(i => `
                        <tr class="border-b border-slate-50 text-slate-700 last:border-0">
                            <td class="py-3 font-medium">${i.producto} <span class="text-slate-400 text-xs">(${i.talla}/${i.color})</span></td>
                            <td class="py-3 text-center text-slate-600">${i.cantidad}</td>
                            <td class="py-3 text-right text-slate-600">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                            <td class="py-3 text-right font-bold text-slate-800">$${(i.cantidad * parseFloat(i.precio_unitario)).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            ${esCompletada ? `
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-sm font-semibold text-slate-700 mb-3">¿Deseas solicitar una devolución?</p>
                    <div id="dev-items" class="space-y-2 mb-3"></div>
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Motivo *</label>
                        <textarea id="inp-motivo" rows="2" placeholder="Describe el motivo de la devolución..."
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 resize-none focus:outline-none focus:border-blue-500 transition"></textarea>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl mb-3">
                        <span class="text-sm font-semibold text-slate-700">Total a devolver</span>
                        <span id="total-dev" class="font-bold text-red-600">$0.00</span>
                    </div>
                    <button onclick="enviarDevolucion(${id})" class="w-full py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition text-sm">
                        <i class="fas fa-undo-alt mr-2 text-xs"></i>Solicitar devolución
                    </button>
                </div>
            ` : ''}
        `;
        if (esCompletada) renderDevItems();
    }

    function renderDevItems() {
        document.getElementById('dev-items').innerHTML = itemsDev.map((it, idx) => `
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl text-sm">
                <div class="flex-1">
                    <p class="font-medium text-slate-800">${it.producto} <span class="text-slate-400 text-xs">(${it.talla}/${it.color})</span></p>
                    <p class="text-xs text-slate-500">Comprado: ${it.cantidad} uds</p>
                </div>
                <div class="flex items-center gap-2 ml-3">
                    <label class="text-xs text-slate-500">Devolver:</label>
                    <input type="number" min="0" max="${it.cantidad}" value="${it.dev_qty}"
                        onchange="updateQty(${idx}, this.value)"
                        class="w-16 px-2 py-1 bg-white border-2 border-slate-200 rounded-lg text-sm text-center">
                </div>
            </div>
        `).join('');
    }

    function updateQty(idx, val) {
        itemsDev[idx].dev_qty = Math.min(parseInt(val) || 0, itemsDev[idx].cantidad);
        calcTotal();
    }

    function calcTotal() {
        const total = itemsDev.reduce((s, i) => s + i.dev_qty * parseFloat(i.precio_unitario), 0);
        document.getElementById('total-dev').textContent = '$' + total.toFixed(2);
    }

    function enviarDevolucion(ventaId) {
        const motivo = document.getElementById('inp-motivo')?.value.trim();
        if (!motivo) {
            Swal.fire('Atención', 'El motivo de la devolución es obligatorio', 'warning');
            return;
        }

        const items = itemsDev.filter(i => i.dev_qty > 0).map(i => ({
            id_producto: i.id_producto,
            cantidad: i.dev_qty,
            precio_unitario: i.precio_unitario
        }));

        if (!items.length) {
            Swal.fire('Atención', 'Selecciona al menos una cantidad a devolver', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('venta_id', ventaId);
        fd.append('motivo', motivo);
        fd.append('items', JSON.stringify(items));

        fetch(DEVOLUCIONES_STORE_URL, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                closeModal();
                mostrarConstancia(d.id_devolucion || 0, motivo, items);
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Error del servidor al registrar devolución.', 'error');
        });
    }

    function mostrarConstancia(id_dev, motivo, itemsDevueltos) {
        const ahora = new Date();
        const fecha = ahora.toLocaleDateString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const hora = ahora.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
        const total = itemsDevueltos.reduce((s, i) => s + i.cantidad * parseFloat(i.precio_unitario), 0);

        const filas = itemsDev.filter(i => i.dev_qty > 0).map(i => `
            <tr class="border-b border-slate-100">
                <td class="py-2 text-slate-700 font-medium">${i.producto} <span class="text-slate-400 text-xs">(${i.talla}/${i.color})</span></td>
                <td class="py-2 text-center text-slate-600">${i.dev_qty}</td>
                <td class="py-2 text-right font-bold text-red-600">-$${(i.dev_qty * parseFloat(i.precio_unitario)).toFixed(2)}</td>
            </tr>
        `).join('');

        document.getElementById('constancia-body').innerHTML = `
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-4 text-center">
                <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">N° de solicitud</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">#${String(id_dev).padStart(5, '0')}</p>
                <p class="text-xs text-slate-500 mt-1">${fecha} a las ${hora}</p>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Motivo registrado</p>
                <p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">${motivo}</p>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Productos devueltos</p>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 text-xs">
                            <th class="pb-1">Producto</th>
                            <th class="text-center pb-1">Cant.</th>
                            <th class="text-right pb-1">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>${filas}</tbody>
                </table>
            </div>
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                <span class="font-semibold text-slate-700 text-sm">Total a reintegrar</span>
                <span class="text-xl font-bold text-red-600">-$${total.toFixed(2)}</span>
            </div>
        `;
        document.getElementById('modal-constancia').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-det').classList.add('hidden');
    }
    function closeConstancia() {
        document.getElementById('modal-constancia').classList.add('hidden');
    }
    function closeConstanciaAndReload() {
        closeConstancia();
        location.reload();
    }
</script>
@endpush
@endsection
