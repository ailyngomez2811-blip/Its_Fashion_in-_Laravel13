@extends('layouts.Sidebarempleado')

@section('titulo', 'Control de Caja (Empleado)')

@section('content')
<div class="p-6 md:p-8 font-sans">
    <!-- Cabecera -->
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 shadow-sm border border-purple-100">
                <i class="fas fa-cash-register text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-serif font-bold text-brand-dark">Caja y Turno</h1>
                <p class="text-brand-muted text-xs font-light">Gestión de flujo de caja y turnos diarios</p>
            </div>
        </div>

        @if ($caja)
            <div class="flex gap-2">
                <button onclick="openMovModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                    <i class="fas fa-exchange-alt mr-2 text-xs"></i>Movimiento manual
                </button>
                <button onclick="openCierreModal()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-red-500/20">
                    <i class="fas fa-lock mr-2 text-xs"></i>Cerrar Caja
                </button>
            </div>
        @else
            <button onclick="openAbrirModal()" class="px-4 py-2.5 bg-brand-accent text-white text-sm font-semibold rounded-xl hover:shadow-lg transition">
                <i class="fas fa-unlock mr-2 text-xs"></i>Abrir turno de caja
            </button>
        @endif
    </div>

    @if ($caja)
        <!-- KPIs Turno Activo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Saldo inicial</p>
                <h3 class="text-xl font-bold text-slate-800">${{ number_format($caja->saldo_inicial, 2) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Apertura: {{ $caja->fecha_apertura ? $caja->fecha_apertura->format('d/m/Y H:i') : '—' }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">Ingresos</p>
                <h3 class="text-xl font-bold text-emerald-600">+${{ number_format($caja->total_ingresos ?? 0, 2) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Entradas de efectivo/ventas</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <p class="text-xs text-red-500 font-bold uppercase tracking-wider mb-1">Egresos</p>
                <h3 class="text-xl font-bold text-red-500">-${{ number_format($caja->total_egresos ?? 0, 2) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Salidas manuales de dinero</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Saldo teórico actual</p>
                <h3 class="text-2xl font-bold text-blue-600">${{ number_format($saldo_teorico, 2) }}</h3>
                <p class="text-[10px] text-slate-400 mt-1">Dinero esperado en caja</p>
            </div>
        </div>

        <!-- Tabla Movimientos -->
        <div class="bg-white rounded-[2rem] border border-slate-100 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Movimientos del Turno</h3>
                <p class="text-xs text-slate-500 mt-0.5">Auditoría en tiempo real de ingresos y egresos</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase font-semibold">
                            <th class="text-left px-6 py-3">Hora</th>
                            <th class="text-left px-6 py-3">Concepto/Descripción</th>
                            <th class="text-left px-6 py-3">Tipo</th>
                            <th class="text-right px-6 py-3">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($movimientos as $mov)
                            <tr class="trow text-slate-700">
                                <td class="px-6 py-4 border-b border-slate-50 text-sm text-slate-500">{{ $mov->fecha ? $mov->fecha->format('H:i') : '—' }}</td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-medium text-slate-800">{{ $mov->descripcion }}</td>
                                <td class="px-6 py-4 border-b border-slate-50">
                                    <span class="badge {{ $mov->tipo === 'Ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $mov->tipo }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right border-b border-slate-50 text-sm font-bold {{ $mov->tipo === 'Ingreso' ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $mov->tipo === 'Ingreso' ? '+' : '-' }}${{ number_format($mov->monto, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center text-slate-400">
                                    <i class="fas fa-exchange-alt text-4xl mb-3 block opacity-20"></i>
                                    <p class="text-sm">No hay movimientos registrados en este turno aún</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Sin Turno Abierto -->
        <div class="bg-white rounded-[2rem] border border-slate-100 p-8 md:p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-slate-100">
                <i class="fas fa-lock text-slate-300 text-3xl"></i>
            </div>
            <h3 class="text-xl font-serif font-bold text-brand-dark mb-2">Caja Cerrada</h3>
            <p class="text-brand-muted font-light max-w-md mx-auto mb-6">
                Para comenzar a facturar y registrar ventas, es necesario realizar la apertura de caja indicando el saldo inicial de efectivo en base.
            </p>
            <button onclick="openAbrirModal()" class="px-6 py-3 bg-brand-accent text-white font-semibold rounded-xl hover:shadow-lg transition">
                <i class="fas fa-unlock mr-2 text-xs"></i>Abrir turno de caja
            </button>
        </div>
    @endif
</div>

<!-- Modal Abrir Caja -->
<div id="modal-abrir" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Apertura de Caja</h3>
        <p class="text-xs text-slate-500 mb-4">Ingresa el saldo base en efectivo que hay en caja física en este momento.</p>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Saldo Inicial ($) *</label>
                <input type="number" id="inp-saldo-inicial" placeholder="0.00" min="0" step="0.01" value="0.00"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 font-bold focus:outline-none focus:border-brand-accent transition">
            </div>
            <div class="flex gap-2 pt-2">
                <button onclick="closeAbrir()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="confirmarApertura()" class="flex-1 py-2.5 bg-brand-accent text-white font-semibold rounded-xl hover:shadow-lg transition text-sm">Abrir Turno</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Movimiento Manual -->
@if ($caja)
<div id="modal-mov" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Registrar Movimiento</h3>
        <p class="text-xs text-slate-500 mb-4">Registra un ingreso o egreso de efectivo manual (p.ej. pago de flete, cambio sencillo).</p>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Tipo de movimiento *</label>
                <select id="inp-mov-tipo" class="w-full py-2.5 px-4 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 cursor-pointer focus:outline-none transition">
                    <option value="Ingreso">Ingreso</option>
                    <option value="Egreso">Egreso</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Concepto/Descripción *</label>
                <input type="text" id="inp-mov-desc" placeholder="Ej: Pago de almuerzo personal, Cambio..."
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Monto ($) *</label>
                <input type="number" id="inp-mov-monto" placeholder="0.00" min="0.01" step="0.01"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none transition">
            </div>
            <div class="flex gap-2 pt-2">
                <button onclick="closeMov()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="confirmarMovimiento()" class="flex-1 py-2.5 bg-brand-accent text-white font-semibold rounded-xl hover:shadow-lg transition text-sm">Registrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->
<div id="modal-cierre" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2">Cierre de Caja</h3>
        <p class="text-xs text-slate-500 mb-4">Ingresa el dinero físico total que contaste físicamente en el cajón.</p>
        <div class="space-y-4">
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs space-y-1">
                <div class="flex justify-between"><span>Base + Ingresos:</span><span class="font-bold text-slate-700">${{ number_format($caja->saldo_inicial + ($caja->total_ingresos ?? 0), 2) }}</span></div>
                <div class="flex justify-between"><span>Egresos totales:</span><span class="font-bold text-red-500">-${{ number_format($caja->total_egresos ?? 0, 2) }}</span></div>
                <div class="flex justify-between border-t border-slate-200 pt-1 font-semibold"><span>Saldo esperado:</span><span class="text-blue-600">${{ number_format($saldo_teorico, 2) }}</span></div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Saldo real físico ($) *</label>
                <input type="number" id="inp-saldo-real" placeholder="0.00" min="0" step="0.01"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 font-bold focus:outline-none focus:border-brand-accent transition">
            </div>
            <div class="flex gap-2 pt-2">
                <button onclick="closeCierre()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">Cancelar</button>
                <button onclick="confirmarCierre()" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl hover:shadow-lg transition text-sm">Cerrar Turno</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const BASE_URL = "{{ url('/caja') }}";

    function openAbrirModal() {
        document.getElementById('inp-saldo-inicial').value = '0.00';
        document.getElementById('modal-abrir').classList.remove('hidden');
    }
    function closeAbrir() {
        document.getElementById('modal-abrir').classList.add('hidden');
    }
    function confirmarApertura() {
        const saldo = parseFloat(document.getElementById('inp-saldo-inicial').value);
        if (isNaN(saldo) || saldo < 0) {
            Swal.fire('Atención', 'Ingresa un saldo inicial válido.', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('saldo_inicial', saldo);
        fd.append('_token', '{{ csrf_token() }}');

        fetch(`${BASE_URL}/abrir`, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                closeAbrir();
                Swal.fire('Éxito', d.msg, 'success').then(() => location.reload());
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        });
    }

    @if ($caja)
    function openMovModal() {
        document.getElementById('inp-mov-tipo').value = 'Ingreso';
        document.getElementById('inp-mov-desc').value = '';
        document.getElementById('inp-mov-monto').value = '';
        document.getElementById('modal-mov').classList.remove('hidden');
    }
    function closeMov() {
        document.getElementById('modal-mov').classList.add('hidden');
    }
    function confirmarMovimiento() {
        const tipo = document.getElementById('inp-mov-tipo').value;
        const desc = document.getElementById('inp-mov-desc').value.trim();
        const monto = parseFloat(document.getElementById('inp-mov-monto').value);

        if (!desc || isNaN(monto) || monto <= 0) {
            Swal.fire('Atención', 'Rellena todos los campos con valores válidos.', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('tipo', tipo);
        fd.append('descripcion', desc);
        fd.append('monto', monto);
        fd.append('_token', '{{ csrf_token() }}');

        fetch(`${BASE_URL}/movimiento`, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                closeMov();
                Swal.fire('Éxito', d.msg, 'success').then(() => location.reload());
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        });
    }

    function openCierreModal() {
        document.getElementById('inp-saldo-real').value = '';
        document.getElementById('modal-cierre').classList.remove('hidden');
    }
    function closeCierre() {
        document.getElementById('modal-cierre').classList.add('hidden');
    }
    function confirmarCierre() {
        const saldo = parseFloat(document.getElementById('inp-saldo-real').value);
        if (isNaN(saldo) || saldo < 0) {
            Swal.fire('Atención', 'Ingresa un saldo real válido.', 'warning');
            return;
        }

        const fd = new FormData();
        fd.append('saldo_real', saldo);
        fd.append('_token', '{{ csrf_token() }}');

        fetch(`${BASE_URL}/cerrar`, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                closeCierre();
                Swal.fire('Éxito', d.msg, 'success').then(() => location.reload());
            } else {
                Swal.fire('Atención', d.msg, 'error');
            }
        });
    }
    @endif
</script>
@endpush
@endsection
