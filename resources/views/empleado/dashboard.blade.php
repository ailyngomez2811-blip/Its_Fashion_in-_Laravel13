@extends('layouts.Sidebarempleado')

@section('titulo', 'Panel Empleado')

@section('content')
<div class="p-6 md:p-8 font-sans">

    <!-- Bienvenida -->
    <div class="mb-6 md:mb-8 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark mb-1">
                Bienvenido, {{ explode(' ', auth()->user()->nombre)[0] }}
            </h2>
            <p class="text-brand-muted font-light text-sm">
                Este es tu panel de trabajo. Tienes acceso a ventas, compras, inventario y caja.
            </p>
        </div>
        <div class="text-sm font-semibold text-slate-500 bg-slate-100 px-4 py-2 rounded-xl">
            <i class="far fa-calendar-alt mr-1.5"></i>{{ today()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Banner devoluciones pendientes -->
    @if ($devolucionesPendientes > 0)
        <div class="flex items-center gap-4 mb-8 p-5 bg-amber-50 border border-amber-200 rounded-2xl shadow-sm">
            <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
                <i class="fas fa-undo-alt text-lg"></i>
            </div>
            <div>
                <p class="text-base font-bold text-amber-800">
                    {{ $devolucionesPendientes }} {{ $devolucionesPendientes == 1 ? 'devolución pendiente' : 'devoluciones pendientes' }} de revisión
                </p>
                <p class="text-sm text-amber-700 font-light mt-0.5">Notifica a tu supervisor para gestionar las solicitudes.</p>
            </div>
        </div>
    @endif

    <!-- KPI Cards del Turno -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Ventas Hoy -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-cash-register text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">Hoy</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Ventas de Hoy</p>
            <h3 class="text-2xl font-bold text-slate-800">${{ number_format($ventasHoy, 2) }}</h3>
            <p class="text-xs mt-2 text-slate-400 font-medium">
                <i class="fas fa-receipt mr-1"></i>{{ $ventasHoyCount }} transacciones
            </p>
        </div>

        <!-- Ingresos del Mes -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 z-0"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600">Mes</span>
                </div>
                <p class="text-sm font-semibold text-slate-500 mb-1">Ingresos del Mes</p>
                <h3 class="text-2xl font-bold text-slate-800">${{ number_format($ingresosMes, 2) }}</h3>
                <p class="text-xs mt-2 text-slate-400 font-medium">
                    <i class="fas fa-check-circle text-emerald-500 mr-1"></i>{{ $ingresosMesCount }} ventas concretadas
                </p>
            </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600">Catálogo</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Prendas Activas</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $productosActivos }}</h3>
            <p class="text-xs mt-2 {{ $productosCriticos > 0 ? 'text-red-500' : 'text-slate-400' }} font-medium">
                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $productosCriticos }} en stock crítico
            </p>
        </div>

        <!-- Devoluciones -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fas fa-undo-alt text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600">Pendientes</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Devoluciones Pendientes</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $devolucionesPendientes }}</h3>
            <p class="text-xs mt-2 text-slate-400 font-medium">
                <i class="fas fa-info-circle mr-1"></i>En espera de revisión
            </p>
        </div>

    </div>

    <!-- Accesos Rápidos del Empleado -->
    <div class="bg-white rounded-[2rem] border border-slate-100 p-6 md:p-8 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Accesos Rápidos</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('ventas.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-blue-300 hover:shadow-md transition-all duration-300 text-center">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-cash-register"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Registrar Venta</span>
            </a>
            <a href="{{ route('compras.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-indigo-300 hover:shadow-md transition-all duration-300 text-center">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Abastecer</span>
            </a>
            <a href="{{ route('inventario.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-md transition-all duration-300 text-center">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-warehouse"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Inventario</span>
            </a>
            <a href="{{ route('caja.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-purple-300 hover:shadow-md transition-all duration-300 text-center">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-3">
                    <i class="fas fa-cash-register"></i>
                </div>
                <span class="text-sm font-bold text-slate-700">Caja</span>
            </a>
        </div>
    </div>

</div>
@endsection
