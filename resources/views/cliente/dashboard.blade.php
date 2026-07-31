@extends('layouts.Sidebarcliente')

@section('titulo', 'Mi Cuenta')

@section('content')
<div class="p-6 md:p-8 font-sans">

    <!-- Banner de bienvenida -->
    <div class="rounded-[2rem] p-6 mb-7 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); box-shadow: 0 8px 24px rgba(37,99,235,.2);">
        <div>
            <p class="text-purple-200 text-sm mb-0.5">Bienvenida de vuelta</p>
            <h2 class="text-2xl font-bold font-serif">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</h2>
            <p class="text-purple-200 text-sm mt-1">{{ auth()->user()->email }}</p>
        </div>
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center border border-white/20 text-2xl font-bold text-white flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->nombre, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">

        <!-- Total compras -->
        <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white"
                     style="background: linear-gradient(135deg,#2563eb,#7c3aed); box-shadow: 0 4px 12px rgba(37,99,235,.3);">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalCompras }}</p>
                    <p class="text-xs text-slate-500">Compras realizadas</p>
                </div>
            </div>
        </div>

        <!-- Total gastado -->
        <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-500 rounded-xl flex items-center justify-center text-white"
                     style="box-shadow: 0 4px 12px rgba(16,185,129,.3);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($totalGastado, 2) }}</p>
                    <p class="text-xs text-slate-500">Total invertido</p>
                </div>
            </div>
        </div>

        <!-- Devoluciones -->
        <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-400 rounded-xl flex items-center justify-center text-white"
                     style="box-shadow: 0 4px 12px rgba(239,68,68,.3);">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalDevoluciones }}</p>
                    <p class="text-xs text-slate-500">Devoluciones</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-7">
        <a href="{{ route('ventas.index') }}"
           class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                 style="background: linear-gradient(135deg,#2563eb,#7c3aed); box-shadow: 0 4px 12px rgba(37,99,235,.3);">
                <i class="fas fa-shopping-bag text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800">Mis compras</p>
                <p class="text-xs text-slate-500 mt-0.5">Ver historial completo de pedidos</p>
            </div>
            <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
        </a>
        <a href="{{ route('devoluciones.index') }}"
           class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 bg-red-400 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                 style="box-shadow: 0 4px 12px rgba(239,68,68,.3);">
                <i class="fas fa-undo-alt text-lg"></i>
            </div>
            <div>
                <p class="font-bold text-slate-800">Mis devoluciones</p>
                <p class="text-xs text-slate-500 mt-0.5">Solicitudes y reembolsos</p>
            </div>
            <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
        </a>
    </div>

    <!-- Tablas de detalle -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Últimas compras -->
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800">Últimas compras</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tu historial reciente</p>
                </div>
                <a href="{{ route('ventas.index') }}" class="text-xs text-purple-600 hover:text-purple-800 font-semibold">
                    Ver todas
                </a>
            </div>
            @if ($misVentas->isEmpty())
                <div class="py-14 text-center text-slate-400">
                    <i class="fas fa-shopping-bag text-4xl mb-3 block opacity-20"></i>
                    <p class="text-sm">Aún no tienes compras registradas</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($misVentas as $v)
                        <div class="flex items-center justify-between px-6 py-3 hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $v->fecha ? $v->fecha->format('d/m/Y') : '—' }} · {{ $v->metodo_pago }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">${{ number_format($v->total, 2) }}</p>
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full {{ $v->estado === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $v->estado }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Devoluciones recientes -->
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-800">Mis devoluciones</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Solicitudes recientes</p>
                </div>
                <a href="{{ route('devoluciones.index') }}" class="text-xs text-purple-600 hover:text-purple-800 font-semibold">
                    Ver todas
                </a>
            </div>
            @if ($misDevoluciones->isEmpty())
                <div class="py-14 text-center text-slate-400">
                    <i class="fas fa-undo-alt text-4xl mb-3 block opacity-20"></i>
                    <p class="text-sm">Sin devoluciones registradas</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($misDevoluciones as $d)
                        <div class="flex items-center justify-between px-6 py-3 hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Venta #{{ str_pad($d->venta_id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-400 truncate max-w-xs">{{ $d->motivo }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">-${{ number_format($d->total_devolucion, 2) }}</p>
                                <p class="text-xs text-slate-400">{{ $d->fecha ? $d->fecha->format('d/m/Y') : '—' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
