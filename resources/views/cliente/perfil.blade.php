@extends('layouts.Sidebarcliente')

@section('titulo', 'Mi Perfil')

@section('content')
<div class="p-6 md:p-10 font-sans max-w-[1600px] mx-auto">
    <!-- Cabecera de Página -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm border border-blue-100/50">
                <i class="fas fa-user-cog text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-serif font-bold text-brand-dark tracking-tight">Mi Perfil</h1>
                <p class="text-brand-muted text-xs md:text-sm font-light mt-0.5">Administra tus datos personales y credenciales de acceso.</p>
            </div>
        </div>
        <div>
            <button onclick="openPasswordModal()" class="flex items-center gap-2 px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:-translate-y-0.5 active:scale-95 transition-all duration-150 shadow-sm border border-slate-200/50">
                <i class="fas fa-key text-xs"></i> <span>Seguridad y Contraseña</span>
            </button>
        </div>
    </div>

    <!-- Alertas -->
    @if (session('status') === 'profile-updated')
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm shadow-sm transition-all duration-300">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="font-semibold">¡Perfil Actualizado!</p>
                <p class="text-xs text-emerald-600/90 font-light">Tus datos personales se han guardado con éxito.</p>
            </div>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm shadow-sm transition-all duration-300">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <p class="font-semibold">¡Contraseña Cambiada!</p>
                <p class="text-xs text-emerald-600/90 font-light">Tu cuenta está ahora protegida con tu nueva clave.</p>
            </div>
        </div>
    @endif

    <!-- Bloque de Contenido en Dos Columnas Ocupando el 100% del Ancho -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
        <!-- Columna Izquierda: Tarjeta Informativa Premium / Avatar -->
        <div class="xl:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] border border-slate-100 p-8 text-center shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                
                <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-3xl flex items-center justify-center text-3xl font-bold text-white mx-auto mb-4 shadow-lg shadow-blue-500/20 border-4 border-white">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}
                </div>
                
                <h3 class="text-lg font-bold text-slate-800 font-serif">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full mt-2 border border-blue-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Cliente Preferencial
                </span>

                <hr class="my-6 border-slate-100">

                <div class="text-left space-y-4 text-sm">
                    <div class="flex items-center gap-3 text-slate-600">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 flex-shrink-0">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <div class="truncate">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Correo Electrónico</p>
                            <p class="font-medium text-slate-700 truncate text-xs">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 text-slate-600">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 flex-shrink-0">
                            <i class="fas fa-phone text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Teléfono de Contacto</p>
                            <p class="font-medium text-slate-700 text-xs">{{ auth()->user()->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Edición Ocupando el 75% Restante -->
        <div class="xl:col-span-3 space-y-8">
            <div class="bg-white rounded-[2rem] border border-slate-100 p-8 md:p-10 shadow-sm transition-all duration-300">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-id-card text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Datos Personales</h3>
                        <p class="text-xs md:text-sm text-slate-400 font-light">Actualiza tu nombre, apellido y vías de contacto primarias.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 block">Nombre *</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-2xl text-sm md:text-base text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                            @error('nombre') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 block">Apellido *</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $user->apellido) }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-2xl text-sm md:text-base text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                            @error('apellido') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 block">Correo Electrónico *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-2xl text-sm md:text-base text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                            @error('email') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 block">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" placeholder="Ej: +57 300 123 4567"
                                class="w-full px-5 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-2xl text-sm md:text-base text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                            @error('telefono') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl hover:shadow-md hover:shadow-blue-500/10 active:scale-98 transition-all duration-150 text-sm">
                            <i class="fas fa-save mr-2 text-xs"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Cambiar Contraseña -->
<div id="password-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8 relative scale-up">
        <button onclick="closePasswordModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-shield-alt text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Seguridad de la Cuenta</h3>
                <p class="text-xs text-slate-400 font-light">Cambia tu contraseña periódicamente.</p>
            </div>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Contraseña Actual *</label>
                <input type="password" name="current_password" required autocomplete="current-password" placeholder="••••••••"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                @error('current_password') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Nueva Contraseña *</label>
                <input type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                @error('password') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Confirmar Nueva Contraseña *</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repite la contraseña"
                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-200">
                @error('password_confirmation') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="closePasswordModal()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-sm">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/20 active:scale-95 transition-all duration-150 text-sm">
                    Guardar Clave
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() {
        document.getElementById('password-modal').classList.remove('hidden');
    }

    function closePasswordModal() {
        document.getElementById('password-modal').classList.add('hidden');
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('password-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePasswordModal();
        }
    });

    // Si hay errores de contraseña al enviar, abrir automáticamente el modal
    @if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        document.addEventListener('DOMContentLoaded', function() {
            openPasswordModal();
        });
    @endif
</script>

<style>
    @keyframes scaleUp {
        from {
            opacity: 0;
            transform: scale(.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .scale-up {
        animation: scaleUp .2s ease-out forwards;
    }
</style>
@endsection
