@extends('layouts.sidebaradmin')

@section('titulo', 'Boutique Dashboard')

@section('content')
<div class="p-6 md:p-8 font-sans">

    <!-- Mensaje de bienvenida -->
    <div class="mb-6 md:mb-8 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark mb-1">
                Bienvenido, {{ explode(' ', auth()->user()->nombre)[0] }}
            </h2>
            <p class="text-brand-muted font-light text-sm md:text-base">
                Aquí tienes un resumen de la actividad de tu boutique hoy.
            </p>
        </div>
        <div class="text-sm font-semibold text-slate-500 bg-slate-100 px-4 py-2 rounded-xl">
            <i class="far fa-calendar-alt mr-1.5"></i>{{ today()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Banner Alerta de Devoluciones Pendientes -->
    @if ($devolucionesPendientes > 0)
        <a href="{{ route('devoluciones.index') }}" 
            class="flex items-center gap-4 mb-8 p-5 bg-amber-50 border border-amber-200 rounded-2xl hover:bg-amber-100/70 transition duration-300 shadow-sm group">
            <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md transition-transform group-hover:scale-105">
                <i class="fas fa-undo-alt text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-base font-bold text-amber-800">
                    Atención requerida: {{ $devolucionesPendientes }} {{ $devolucionesPendientes == 1 ? 'devolución pendiente' : 'devoluciones pendientes' }}
                </p>
                <p class="text-sm text-amber-700 font-light mt-0.5">Haz clic aquí para revisar y resolver las solicitudes pendientes de reembolso.</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-white border border-amber-200 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition duration-300">
                <i class="fas fa-arrow-right text-xs"></i>
            </div>
        </a>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Ventas Hoy -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">Hoy</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Ventas de Hoy</p>
            <h3 class="text-2xl font-bold text-slate-800">${{ number_format($ventasHoy, 2) }}</h3>
            <p class="text-xs mt-2 text-slate-400 font-medium">
                <i class="fas fa-receipt mr-1"></i>{{ $ventasHoyCount }} transacciones
            </p>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-600">Catálogo</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Prendas en Catálogo</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $totalProductos }}</h3>
            <p class="text-xs mt-2 text-slate-400 font-medium">
                <i class="fas fa-check-circle text-emerald-500 mr-1"></i>{{ $productosActivos }} activos en tienda
            </p>
        </div>

        <!-- Ingresos -->
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

        <!-- Clientes -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-500">
                    <i class="fas fa-user-friends text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-600">Comunidad</span>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Clientes Registrados</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $totalClientes }}</h3>
            <p class="text-xs mt-2 text-slate-400 font-medium">
                <i class="fas fa-users mr-1"></i>{{ $clientesActivos }} activos
            </p>
        </div>

    </div>

    @if ($totalProductos === 0 && $totalClientes === 0)
        <!-- Empty state -->
        <div class="bg-white rounded-[2rem] border border-slate-100 p-8 md:p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-gray-100">
                <i class="fas fa-store-slash text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-xl font-serif font-bold text-brand-dark mb-2">Tu boutique está lista</h3>
            <p class="text-brand-muted font-light max-w-md mx-auto">
                Aún no hay datos registrados. Comienza agregando categorías y productos a tu inventario para ver las estadísticas aquí.
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('categorias.index') }}" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/20">
                    <i class="fas fa-plus mr-2"></i>Ver Categorías
                </a>
            </div>
        </div>
    @else
        <!-- Panel de control de accesos rápidos -->
        <div class="bg-white rounded-[2rem] border border-slate-100 p-6 md:p-8 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Accesos Rápidos del Administrador</h3>
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
                    <span class="text-sm font-bold text-slate-700">Abastecer Stock</span>
                </a>
                <a href="{{ route('inventario.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-emerald-300 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Kardex / Stock</span>
                </a>
                <a href="{{ route('reportes.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-purple-300 hover:shadow-md transition-all duration-300 text-center">
                    <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Ver Reportes</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection