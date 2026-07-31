@extends('layouts.sidebaradmin')

@section('titulo', 'Devoluciones')

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
        max-width: 580px;
        max-height: 90vh;
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
</style>
@endpush

@section('content')
<div class="p-6 font-sans">

    <!-- Encabezado de la página -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-brand-accent shadow-sm border border-blue-100">
                <i class="fas fa-undo-alt text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Devoluciones</h1>
        </div>
    </div>

    <!-- KPIs de Devoluciones -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-amber-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800" id="kpi-pendientes">{{ $kpi['pendientes'] }}</p>
                    <p class="text-xs text-slate-500">Solicitudes pendientes</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 stat-card">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-hand-holding-usd text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($kpi['total_monto'], 2) }}</p>
                    <p class="text-xs text-slate-500">Monto total devuelto (Aceptadas)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Directorio de Devoluciones -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-800">Historial de solicitudes</h3>
                <p class="text-xs text-slate-500 mt-0.5"><span id="cant-devoluciones">{{ count($devoluciones) }}</span> solicitud(es) registrada(s)</p>
            </div>

            <!-- Botones de filtrado rápido -->
            <div class="flex flex-wrap gap-2">
                <button onclick="filtrar('todos')" id="btn-todos" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white transition shadow-sm">Todos</button>
                <button onclick="filtrar('Pendiente')" id="btn-Pendiente" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-amber-100 hover:text-amber-700 transition">Pendientes</button>
                <button onclick="filtrar('Aceptada')" id="btn-Aceptada" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700 transition">Aceptadas</button>
                <button onclick="filtrar('Rechazada')" id="btn-Rechazada" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-red-100 hover:text-red-700 transition">Rechazadas</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['#', 'Fecha', 'Venta Original', 'Cliente', 'Motivo', 'Total Devolución', 'Estado', 'Acciones'] as $h)
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    @forelse ($devoluciones as $d)
                    @php
                    $estadoClasses = [
                    'Pendiente' => 'bg-amber-100 text-amber-700',
                    'Aceptada' => 'bg-emerald-100 text-emerald-700',
                    'Rechazada' => 'bg-red-100 text-red-700',
                    ];
                    $cls = $estadoClasses[$d->estado] ?? 'bg-slate-100 text-slate-600';
                    @endphp
                    <tr class="trow text-slate-700" data-estado="{{ $d->estado }}">
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">#{{ $d->id }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $d->fecha ? $d->fecha->format('d/m/Y H:i') : '—' }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-blue-600 font-semibold">#{{ $d->venta_id }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">
                            {{ $d->venta->cliente->nombre ?? 'Mostrador' }} {{ $d->venta->cliente->apellido ?? '' }}
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500 max-w-xs truncate" title="{{ $d->motivo }}">
                            {{ $d->motivo }}
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-red-600">${{ number_format($d->total_devolucion, 2) }}</td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <span class="badge {{ $cls }}">
                                {{ $d->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-1.5">
                                <button onclick="verDetalle({{ $d->id }})" title="Ver detalle"
                                    class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                @if ($d->estado === 'Pendiente' && Auth::user()->rol_id === 1)
                                <button onclick="resolver({{ $d->id }}, 'aprobar')" title="Aprobar Devolución"
                                    class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-200 text-emerald-600 flex items-center justify-center transition">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                                <button onclick="resolver({{ $d->id }}, 'rechazar')" title="Rechazar Devolución"
                                    class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-200 text-red-500 flex items-center justify-center transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="row-empty">
                        <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-undo-alt text-4xl mb-3 block opacity-20"></i>
                            <p class="text-sm">No hay solicitudes de devolución registradas aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Paginador de Devoluciones -->
            <div id="returns-pagination" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2 bg-slate-50/50">
                <span class="text-xs text-slate-500 font-medium" id="returns-page-info">Mostrando registros 1-10</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevReturnsPage()" id="btn-returns-prev" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Anterior</button>
                    <button type="button" onclick="nextReturnsPage()" id="btn-returns-next" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-50 transition">Siguiente</button>
                </div>
            </div>
        </div>
        <div id="empty-state" class="hidden py-16 text-center text-slate-400">
            <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No se encontraron devoluciones con ese criterio</p>
        </div>
    </div>
</div>

<!-- MODAL VER DETALLE DE SOLICITUD -->
<div id="modal-det" class="modal-overlay hidden">
    <div class="modal-box scale-up">
        <div class="flex items-center justify-between p-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <h3 class="text-lg font-bold text-slate-800">Detalle de Solicitud</h3>
            <button onclick="closeDet()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div id="det-body" class="p-6 overflow-y-auto flex-1">
            <!-- Dibujado por Fetch AJAX -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const BASE_URL = "{{ url('/devoluciones') }}";

    let currentReturnsPage = 1;
    const returnsRecordsPerPage = 10;

    document.addEventListener('DOMContentLoaded', () => {
        applyReturnsPagination();
    });

    function applyReturnsPagination() {
        const rows = Array.from(document.querySelectorAll('#tabla-body tr')).filter(row => row.dataset.estado && row.style.display !== 'none');
        const infoSpan = document.getElementById('returns-page-info');
        const prevBtn = document.getElementById('btn-returns-prev');
        const nextBtn = document.getElementById('btn-returns-next');

        const total = rows.length;
        const totalPages = Math.ceil(total / returnsRecordsPerPage) || 1;

        if (currentReturnsPage > totalPages) {
            currentReturnsPage = totalPages;
        }

        const startIdx = (currentReturnsPage - 1) * returnsRecordsPerPage;
        const endIdx = startIdx + returnsRecordsPerPage;

        const allRows = document.querySelectorAll('#tabla-body tr');
        let matchedIdx = 0;
        allRows.forEach(row => {
            if (row.dataset.estado) {
                if (row.getAttribute('data-filtered') === 'true') {
                    row.style.display = 'none';
                } else {
                    if (matchedIdx >= startIdx && matchedIdx < endIdx) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                    matchedIdx++;
                }
            }
        });

        if (total === 0) {
            infoSpan.textContent = "Mostrando registros 0 de 0";
            prevBtn.disabled = true;
            nextBtn.disabled = true;
        } else {
            infoSpan.textContent = `Mostrando registros ${startIdx + 1}-${Math.min(endIdx, total)} de ${total}`;
            prevBtn.disabled = currentReturnsPage === 1;
            nextBtn.disabled = currentReturnsPage === totalPages;
        }
    }

    function prevReturnsPage() {
        if (currentReturnsPage > 1) {
            currentReturnsPage--;
            applyReturnsPagination();
        }
    }

    function nextReturnsPage() {
        currentReturnsPage++;
        applyReturnsPagination();
    }

    // ── Filtro local reactivo por estado ───────────────────────────────────────────
    function filtrar(estado) {
        // Estilo de los botones
        ['todos', 'Pendiente', 'Aceptada', 'Rechazada'].forEach(est => {
            const btn = document.getElementById('btn-' + est);
            if (est === estado) {
                btn.className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white transition shadow-sm";
            } else {
                let hoverCls = "hover:bg-slate-200";
                if (est === 'Pendiente') hoverCls = "hover:bg-amber-100 hover:text-amber-700";
                if (est === 'Aceptada') hoverCls = "hover:bg-emerald-100 hover:text-emerald-700";
                if (est === 'Rechazada') hoverCls = "hover:bg-red-100 hover:text-red-700";

                btn.className = `px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 ${hoverCls} transition`;
            }
        });

        let filtrados = 0;
        let total = 0;

        document.querySelectorAll('#tabla-body tr[data-estado]').forEach(row => {
            total++;
            const show = (estado === 'todos' || row.dataset.estado === estado);
            if (show) {
                row.removeAttribute('data-filtered');
                filtrados++;
            } else {
                row.setAttribute('data-filtered', 'true');
            }
        });

        document.getElementById('cant-devoluciones').textContent = filtrados;

        const rowEmpty = document.getElementById('row-empty');
        if (!rowEmpty) {
            document.getElementById('empty-state').classList.toggle('hidden', filtrados > 0);
        }

        currentReturnsPage = 1;
        applyReturnsPagination();
    }

    function closeDet() {
        document.getElementById('modal-det').classList.add('hidden');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDet();
    });

    // ── Aprobación / Rechazo (Fetch POST) ──────────────────────────────────────────
    function resolver(id, accion) {
        const label = accion === 'aprobar' ? 'APROBAR' : 'RECHAZAR';
        const icon = accion === 'aprobar' ? 'question' : 'warning';
        const color = accion === 'aprobar' ? '#10b981' : '#ef4444';

        Swal.fire({
            title: `¿Estás seguro?`,
            text: `Vas a ${label} esta solicitud de devolución. Esta acción no se puede deshacer.`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6b7280',
            confirmButtonText: accion === 'aprobar' ? 'Sí, aprobar' : 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');

                fetch(`${BASE_URL}/${id}/${accion}`, {
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
                            Swal.fire({
                                icon: 'success',
                                title: '¡Resuelto!',
                                text: d.msg,
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
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
                            text: err.message.substring(0, 200) || 'Ocurrió un error inesperado al procesar la resolución.'
                        });
                    });
            }
        });
    }

    // ── Ver Detalle de la Solicitud (Fetch AJAX) ──────────────────────────────────
    function verDetalle(id) {
        fetch(`${BASE_URL}/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                const body = document.getElementById('det-body');

                body.innerHTML = `
                <div class="mb-5 p-4 bg-slate-50 rounded-2xl text-sm space-y-2.5 text-slate-700">
                    <p><strong class="text-slate-500 font-bold uppercase text-xs block mb-0.5">Asociada a Venta</strong> <span class="font-mono text-blue-600 font-semibold">#${data.venta_id}</span></p>
                    <p><strong class="text-slate-500 font-bold uppercase text-xs block mb-0.5">Motivo del reclamo</strong> <span class="italic text-slate-800">"${data.motivo}"</span></p>
                    <p><strong class="text-slate-500 font-bold uppercase text-xs block mb-0.5">Estado de solicitud</strong> <span class="font-bold">${data.estado}</span></p>
                </div>
                <table class="w-full text-sm mb-4 whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-2 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">Cant. Devuelta</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">Precio Unit.</th>
                            <th class="text-right py-2 text-xs font-semibold text-slate-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.items.map(i => `
                            <tr class="border-t border-slate-50 trow">
                                <td class="py-3 font-medium text-slate-800">${i.producto} <span class="text-slate-400 text-xs">(${i.talla}/${i.color})</span></td>
                                <td class="py-3 text-right text-slate-700">${i.cantidad}</td>
                                <td class="py-3 text-right text-slate-700">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
                                <td class="py-3 text-right font-bold text-red-600">$${parseFloat(i.subtotal).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl">
                    <span class="font-semibold text-slate-700">Total Devuelto</span>
                    <span class="text-xl font-bold text-red-600">$${parseFloat(data.total_devolucion).toFixed(2)}</span>
                </div>
            `;
                document.getElementById('modal-det').classList.remove('hidden');
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener los detalles de la solicitud.'
                });
            });
    }

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