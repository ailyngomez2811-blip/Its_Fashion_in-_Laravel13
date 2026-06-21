@extends('layouts.sidebaradmin')

@section('titulo', 'Dashboard')

@section('content')
<div class="p-4 md:p-8 font-sans">

    <div class="mb-6 md:mb-8">
        <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark mb-2">
            Bienvenido, {{ explode(' ', auth()->user()->nombre)[0] }}
        </h2>
        <p class="text-brand-muted font-light text-sm md:text-base">
            Aquí tienes un resumen de la actividad de tu boutique hoy.
        </p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Ventas Hoy -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-brand-accent text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-blue-50 text-brand-accent">Hoy</span>
            </div>
            <p class="text-sm font-medium text-brand-muted mb-1">Ventas de Hoy</p>
            <h3 class="text-3xl font-serif font-bold text-brand-dark">$0</h3>
            <p class="text-xs mt-2 text-gray-400 font-light"><i class="fas fa-info-circle mr-1"></i>Módulo de ventas pendiente</p>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-box-open text-indigo-500 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-indigo-50 text-indigo-600">Inventario</span>
            </div>
            <p class="text-sm font-medium text-brand-muted mb-1">Productos Activos</p>
            <h3 class="text-3xl font-serif font-bold text-brand-dark">{{ $totalProductos ?? 0 }}</h3>
            <p class="text-xs mt-2 text-gray-400 font-light"><i class="fas fa-exclamation-triangle mr-1"></i>Módulo de productos pendiente</p>
        </div>

        <!-- Ingresos -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full opacity-50 z-0"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-chart-line text-emerald-500 text-xl"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-600">Este Mes</span>
                </div>
                <p class="text-sm font-medium text-brand-muted mb-1">Ingresos del Mes</p>
                <h3 class="text-3xl font-serif font-bold text-brand-dark">$0</h3>
                <p class="text-xs mt-2 text-gray-400 font-light"><i class="fas fa-info-circle mr-1"></i>Módulo de ventas pendiente</p>
            </div>
        </div>

        <!-- Clientes -->
        <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-user-friends text-purple-500 text-xl"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-purple-50 text-purple-600">Comunidad</span>
            </div>
            <p class="text-sm font-medium text-brand-muted mb-1">Clientes Registrados</p>
            <h3 class="text-3xl font-serif font-bold text-brand-dark">{{ $totalClientes }}</h3>
            <p class="text-xs mt-2 text-gray-400 font-light"><i class="fas fa-users mr-1"></i>{{ $clientesActivos }} activos</p>
        </div>

    </div>

    <!-- Empty state -->
    <div class="bg-white rounded-[2rem] border border-gray-100 p-8 md:p-16 text-center shadow-sm">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-gray-100">
            <i class="fas fa-store-slash text-gray-300 text-3xl"></i>
        </div>
        <h3 class="text-xl font-serif font-bold text-brand-dark mb-2">Tu boutique está lista</h3>
        <p class="text-brand-muted font-light max-w-md mx-auto">
            Aún no hay datos registrados. Comienza agregando categorías y productos a tu inventario para ver las estadísticas aquí.
        </p>
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('categorias.index') }}" class="px-6 py-2.5 bg-brand-accent text-white font-medium rounded-full hover:bg-blue-700 transition-colors shadow-md shadow-blue-600/20">
                <i class="fas fa-plus mr-2"></i>Ver Categorías
            </a>
        </div>
    </div>

</div>
@endsection