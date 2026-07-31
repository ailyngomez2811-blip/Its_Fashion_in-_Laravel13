@extends('layouts.Sidebarcliente')

@section('titulo', 'Mis Devoluciones')

@section('content')
<div class="p-6 md:p-8 font-sans">
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark mb-1">Mis Devoluciones</h2>
            <p class="text-brand-muted font-light text-sm">Historial de solicitudes de devolución y reembolsos</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $misDevoluciones->where('estado', 'Pendiente')->count() }}</p>
                    <p class="text-xs text-slate-500">En revisión</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-undo-alt text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ $totalDevoluciones }}</p>
                    <p class="text-xs text-slate-500">Solicitudes totales</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-emerald-700">
                        ${{ number_format($misDevoluciones->where('estado', 'Aceptada')->sum('total_devolucion'), 2) }}
                    </p>
                    <p class="text-xs text-slate-500">Monto reintegrado</p>
                </div>
            </div>
        </div>
    </div>

    @if ($misDevoluciones->where('estado', 'Pendiente')->count() > 0)
        <div class="mb-6 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-800">
            <i class="fas fa-info-circle text-amber-500 flex-shrink-0"></i>
            Tienes solicitudes pendientes de revisión por el administrador.
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Historial de solicitudes</h3>
            <p class="text-xs text-slate-500 mt-0.5">Estado de resolución por la administración</p>
        </div>

        @if ($misDevoluciones->isEmpty())
            <div class="py-20 text-center text-slate-400">
                <i class="fas fa-undo-alt text-5xl mb-4 block opacity-20"></i>
                <p class="text-lg font-semibold text-slate-600">Aún no tienes solicitudes</p>
                <p class="text-sm mt-1">Puedes solicitar una devolución desde tu historial de compras.</p>
                <a href="{{ route('ventas.index') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full hover:shadow-lg transition">Ir a Mis compras</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase font-semibold">
                            <th class="text-left px-6 py-3">ID Solicitud</th>
                            <th class="text-left px-6 py-3">Venta</th>
                            <th class="text-left px-6 py-3">Fecha Solicitud</th>
                            <th class="text-left px-6 py-3">Motivo</th>
                            <th class="text-left px-6 py-3">Monto</th>
                            <th class="text-left px-6 py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($misDevoluciones as $d)
                            <tr class="hover:bg-slate-50 transition-colors text-slate-700">
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-slate-500">
                                    #{{ str_pad($d->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-mono text-blue-500">
                                    #{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm">
                                    {{ $d->fecha ? $d->fecha->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm truncate max-w-xs" title="{{ $d->motivo }}">
                                    {{ $d->motivo }}
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm font-bold text-red-600">
                                    -${{ number_format($d->total_devolucion, 2) }}
                                </td>
                                <td class="px-6 py-4 border-b border-slate-50 text-sm">
                                    <span class="badge {{ $d->estado === 'Pendiente' ? 'bg-amber-100 text-amber-700' : ($d->estado === 'Aceptada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $d->estado }}
                                    </span>
                                    @if ($d->fecha_resolucion)
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $d->fecha_resolucion->format('d/m/Y') }}</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
