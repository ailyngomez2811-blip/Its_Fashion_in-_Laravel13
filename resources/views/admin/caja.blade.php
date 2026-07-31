@extends('layouts.sidebaradmin')

@section('titulo', 'Caja')

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
        max-width: 480px;
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
                <i class="fas fa-cash-register text-lg"></i>
            </div>
            <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Arqueo de Caja</h1>
        </div>

        <div class="flex gap-3">
            @if (!$caja)
            <button onclick="openModal('apertura')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl hover:shadow-lg transition-all">
                <i class="fas fa-play text-xs"></i>Abrir Caja
            </button>
            @else
            <button onclick="openModal('movimiento')"
                class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-sm transition flex items-center gap-2">
                <i class="fas fa-exchange-alt text-xs"></i>Movimiento Manual
            </button>
            <button onclick="openModal('cierre')"
                class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 hover:shadow-md transition flex items-center gap-2">
                <i class="fas fa-stop-circle text-xs"></i>Cerrar Caja
            </button>
            @endif
        </div>
    </div>

    <!-- Banner Estado -->
    <div class="flex items-center justify-between p-6 rounded-2xl mb-6 {{ $caja ? 'bg-blue-600 text-white shadow-blue-500/20' : 'bg-slate-800 text-white shadow-slate-500/20' }} shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center border-2 {{ $caja ? 'bg-white/20 border-white/20' : 'bg-white/10 border-white/10' }}">
                <i class="fas fa-cash-register text-xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full {{ $caja ? 'bg-green-400' : 'bg-red-400' }}"
                        style="{{ $caja ? 'box-shadow:0 0 8px #4ade80' : '' }}"></div>
                    <span class="font-bold text-lg">Caja {{ $caja ? 'ABIERTA' : 'CERRADA' }}</span>
                </div>
                <p class="text-blue-100 text-sm opacity-90">
                    @if ($caja)
                    Apertura: {{ $caja->fecha_apertura ? $caja->fecha_apertura->format('d/m/Y H:i') : '—' }} · Responsable: {{ $caja->usuario->nombre ?? '' }} {{ $caja->usuario->apellido ?? '' }}
                    @else
                    Sin turno de caja activo en el sistema.
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if ($caja)
    <!-- KPIs de la Caja Activa -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-play text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($caja->saldo_inicial, 2) }}</p>
                    <p class="text-xs text-slate-500">Saldo inicial</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-down text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($caja->total_ingresos ?? 0, 2) }}</p>
                    <p class="text-xs text-slate-500">Ingresos (+)</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 text-red-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($caja->total_egresos ?? 0, 2) }}</p>
                    <p class="text-xs text-slate-500">Egresos (-)</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 stat-card shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calculator text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">${{ number_format($saldo_teorico, 2) }}</p>
                    <p class="text-xs text-slate-500">Saldo teórico</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos del Turno -->
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm" style="box-shadow:0 2px 16px rgba(0,0,0,.04);">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Movimientos del turno</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ today()->format('d/m/Y') }}</p>
        </div>
        @if (empty($movimientos) || count($movimientos) === 0)
        <div class="py-16 text-center text-slate-400">
            <i class="fas fa-exchange-alt text-3xl mb-3 block opacity-20"></i>
            <p class="text-sm">No hay movimientos en este turno de caja aún</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr>
                        @foreach (['Hora', 'Tipo', 'Concepto', 'Monto'] as $h)
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide bg-slate-50 border-b border-slate-100">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $m)
                    <tr class="trow text-slate-700">
                        <td class="px-6 py-4 border-b border-slate-50">
                            <span class="font-mono text-xs text-slate-500">{{ $m->fecha ? $m->fecha->format('H:i') : '' }}</span>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50">
                            <span class="badge {{ $m->tipo === 'Ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $m->tipo }}</span>
                        </td>
                        <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-700">{{ $m->concepto }}</td>
                        <td class="px-6 py-4 border-b border-slate-50 font-bold {{ $m->tipo === 'Ingreso' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->tipo === 'Ingreso' ? '+' : '-' }}${{ number_format($m->monto, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @else
    <!-- Caja Cerrada Mensaje Central -->
    <div class="py-20 text-center text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <i class="fas fa-cash-register text-5xl mb-4 block opacity-20"></i>
        <p class="text-lg font-semibold text-slate-600">Caja sin turno activo</p>
        <p class="text-sm mt-1 mb-6">Abre la caja ingresando el efectivo inicial para comenzar a operar.</p>
        <button onclick="openModal('apertura')"
            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl hover:shadow-lg transition-all">
            <i class="fas fa-play text-xs"></i>Abrir Turno de Caja
        </button>
    </div>
    @endif

    <!-- MODAL GESTOR CAJA -->
    <div id="modal" class="modal-overlay hidden">
        <div class="modal-box scale-up">
            <div class="flex items-start justify-between p-7 pb-0">
                <div>
                    <h3 id="modal-title" class="text-xl font-bold text-slate-800"></h3>
                    <p id="modal-sub" class="text-sm text-slate-500 mt-1"></p>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition flex-shrink-0 ml-4">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-7 overflow-y-auto" id="modal-body">
                <!-- Inyectado por JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const ABRIR_URL = "{{ route('caja.abrir') }}";
    const CERRAR_URL = "{{ route('caja.cerrar') }}";
    const MOVIMIENTO_URL = "{{ route('caja.movimiento') }}";

    const SALDO_TEORICO = parseFloat("{{ $saldo_teorico }}");
    const ID_CAJA = parseInt("{{ $caja->id ?? 0 }}");
    const RESPONSABLE = "{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}";

    function openModal(type) {
        document.getElementById('modal').classList.remove('hidden');
        const body = document.getElementById('modal-body');

        if (type === 'apertura') {
            document.getElementById('modal-title').textContent = 'Apertura de caja';
            document.getElementById('modal-sub').textContent = 'Registra el efectivo disponible al iniciar el turno';
            body.innerHTML = `
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Monto inicial en efectivo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                        <input type="number" id="f-monto" placeholder="0" min="0" step="any"
                            class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-brand-accent transition">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Responsable</label>
                    <input type="text" value="${RESPONSABLE}" readonly class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm opacity-60">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                    <button onclick="submitModal('apertura')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                        <i class="fas fa-play text-xs"></i>Abrir Caja
                    </button>
                </div>
            `;
        }

        if (type === 'movimiento') {
            document.getElementById('modal-title').textContent = 'Movimiento manual de caja';
            document.getElementById('modal-sub').textContent = 'Registra un ingreso o egreso de efectivo';
            body.innerHTML = `
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipo</label>
                    <select id="f-tipo" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm cursor-pointer focus:outline-none focus:border-brand-accent transition">
                        <option value="Ingreso">Ingreso</option>
                        <option value="Egreso">Egreso</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Concepto <span class="text-red-500">*</span></label>
                    <input type="text" id="f-concepto" placeholder="Describe el motivo del movimiento..." 
                        class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-brand-accent transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Monto <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                        <input type="number" id="f-monto" placeholder="0" min="0.01" step="any"
                            class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-brand-accent transition">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                    <button onclick="submitModal('movimiento')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition" style="box-shadow:0 4px 12px rgba(59,130,246,.25);">
                        <i class="fas fa-save text-xs"></i>Registrar
                    </button>
                </div>
            `;
        }

        if (type === 'cierre') {
            document.getElementById('modal-title').textContent = 'Cierre de caja';
            document.getElementById('modal-sub').textContent = 'Realiza el arqueo antes de cerrar el turno';
            body.innerHTML = `
                <div class="p-4 bg-slate-50 rounded-xl flex justify-between items-center mb-4">
                    <span class="font-semibold text-slate-700 text-sm">Saldo teórico del sistema</span>
                    <span class="font-bold text-blue-600 text-xl">$${SALDO_TEORICO.toLocaleString('es-CO', { minimumFractionDigits: 2 })}</span>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Conteo físico de efectivo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm pointer-events-none">$</span>
                        <input type="number" id="f-conteo" placeholder="0" min="0" step="any" oninput="calcDif()" 
                            class="w-full pl-7 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm focus:outline-none focus:border-brand-accent transition">
                    </div>
                </div>
                <div id="dif-preview" class="hidden p-3 rounded-xl text-sm mb-3"></div>
                
                <div id="panel-justif" class="hidden mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Justificación de la diferencia <span class="text-red-500">*</span></label>
                    <textarea id="f-justif" rows="3" placeholder="Explique el motivo del faltante o sobrante..." 
                        class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-700 text-sm resize-none focus:outline-none focus:border-brand-accent transition"></textarea>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button onclick="closeModal()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                    <button onclick="submitModal('cierre')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition">
                        <i class="fas fa-stop-circle text-xs"></i>Cerrar Caja
                    </button>
                </div>
            `;
        }
    }

    function calcDif() {
        const val = document.getElementById('f-conteo').value;
        if (val === '') {
            document.getElementById('dif-preview').classList.add('hidden');
            document.getElementById('panel-justif').classList.add('hidden');
            return;
        }

        const conteo = parseFloat(val) || 0;
        const dif = conteo - SALDO_TEORICO;
        const prev = document.getElementById('dif-preview');
        const justif = document.getElementById('panel-justif');

        prev.classList.remove('hidden');
        if (Math.abs(dif) < 0.01) {
            prev.className = 'p-3 rounded-xl text-sm mb-3 bg-emerald-50 border border-emerald-200 text-emerald-700';
            prev.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Sin diferencia. El efectivo coincide perfectamente.';
            justif.classList.add('hidden');
        } else {
            prev.className = `p-3 rounded-xl text-sm mb-3 ${dif > 0 ? 'bg-blue-50 border border-blue-200 text-blue-700' : 'bg-red-50 border border-red-200 text-red-700'}`;
            prev.innerHTML = `<i class="fas fa-${dif > 0 ? 'info-circle' : 'exclamation-triangle'} mr-1"></i>${dif > 0 ? 'Sobrante' : 'Faltante'} detectado: <b>$${Math.abs(dif).toLocaleString('es-CO', { minimumFractionDigits: 2 })}</b>`;
            justif.classList.remove('hidden');
        }
    }

    function submitModal(type) {
        const fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');

        if (type === 'apertura') {
            const monto = document.getElementById('f-monto').value;
            if (!monto || parseFloat(monto) < 0) {
                showToast('Ingresa un monto inicial válido.', 'error');
                return;
            }
            fd.append('saldo_inicial', monto);

            fetch(ABRIR_URL, {
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
                            title: '¡Caja Abierta!',
                            text: d.msg,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        showToast(d.msg, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message.substring(0, 200) || 'Error al abrir la caja.'
                    });
                });
        }

        if (type === 'movimiento') {
            const tipo = document.getElementById('f-tipo').value;
            const concepto = document.getElementById('f-concepto').value.trim();
            const monto = document.getElementById('f-monto').value;

            if (!concepto || !monto || parseFloat(monto) <= 0) {
                showToast('Completa todos los campos con valores válidos.', 'error');
                return;
            }

            fd.append('id_caja', ID_CAJA);
            fd.append('tipo', tipo);
            fd.append('concepto', concepto);
            fd.append('monto', monto);

            fetch(MOVIMIENTO_URL, {
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
                            title: '¡Movimiento Registrado!',
                            text: d.msg,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        showToast(d.msg, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message.substring(0, 200) || 'Error al guardar el movimiento.'
                    });
                });
        }

        if (type === 'cierre') {
            const conteo = document.getElementById('f-conteo').value;
            const justif = document.getElementById('f-justif')?.value || '';

            if (conteo === '' || parseFloat(conteo) < 0) {
                showToast('Ingresa el monto contado físicamente.', 'error');
                return;
            }

            const dif = parseFloat(conteo) - SALDO_TEORICO;
            if (Math.abs(dif) >= 0.01 && !justif.trim()) {
                showToast('La justificación es obligatoria cuando existe diferencia en el conteo.', 'error');
                return;
            }

            fd.append('id_caja', ID_CAJA);
            fd.append('saldo_final', conteo);
            fd.append('justificacion', justif);

            fetch(CERRAR_URL, {
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
                            title: '¡Caja Cerrada!',
                            text: d.msg,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        showToast(d.msg, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message.substring(0, 200) || 'Error al cerrar la caja.'
                    });
                });
        }
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    // Cerrar modal al hacer click fuera de la caja
    document.getElementById('modal').addEventListener('click', e => {
        if (e.target === document.getElementById('modal')) closeModal();
    });

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