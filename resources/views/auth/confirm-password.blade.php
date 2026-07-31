<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirmar contraseña en Its Fashion">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | Confirmar Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/icono head .png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0f172a',
                            light: '#f8fafc',
                            accent: '#2563eb',
                            muted: '#64748b'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .hero-overlay {
            background: linear-gradient(to right, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.6) 100%);
        }
    </style>
</head>

<body class="bg-brand-light text-brand-dark min-h-screen flex items-center justify-center p-4 sm:p-8 antialiased">

    <div class="w-11/12 max-w-[90vw] bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">

        <!-- Left Panel - Branding (Image + Overlay) -->
        <div class="md:w-5/12 relative hidden md:flex flex-col justify-between overflow-hidden">
            <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Boutique">
            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative z-10 p-12">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="{{ asset('img/logo en blanco.png') }}" alt="Its Fashion Logo" class="w-12 h-auto object-contain">
                    <span class="font-serif text-2xl font-bold text-white tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                </a>
            </div>

            <div class="relative z-10 p-12 mt-auto">
                <h2 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">Área segura<br>de la aplicación</h2>
                <p class="text-gray-300 font-light text-sm leading-relaxed mb-8">
                    Por favor confirma tu contraseña actual antes de continuar con la acción solicitada para resguardar la seguridad de tus datos.
                </p>
                <div class="text-[10px] text-gray-400 font-medium tracking-widest uppercase">
                    Its Fashion &copy; {{ date('Y') }} — Sistema de Gestión
                </div>
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="flex-1 p-8 sm:p-12 md:p-16 flex flex-col justify-center bg-white">
            <div class="w-full max-w-md mx-auto">
                
                <!-- Logo para Mobile -->
                <div class="flex md:hidden justify-center mb-8">
                    <div class="inline-flex items-center gap-2">
                        <img src="{{ asset('img/logo original.png') }}" alt="Its Fashion Logo" class="w-10 h-auto">
                        <span class="font-serif text-xl font-bold text-brand-dark tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                    </div>
                </div>

                <div class="mb-8 text-center md:text-left">
                    <h1 class="text-3xl font-serif font-bold text-brand-dark mb-2">Confirmar Contraseña</h1>
                    <p class="text-sm text-brand-muted font-light leading-relaxed">
                        Esta es una sección segura del sistema. Por favor, confirma tu contraseña actual para continuar.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                    @csrf

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Contraseña Actual</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 pointer-events-none">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" id="password" name="password" required autocomplete="current-password" autofocus
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-accent focus:bg-white transition-all duration-200 @error('password') border-red-300 focus:border-red-500 @enderror"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 flex items-center gap-1.5 mt-1">
                                <i class="fas fa-exclamation-circle text-[10px]"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <button type="submit"
                        class="w-full py-4 bg-brand-dark hover:bg-slate-800 text-white font-bold rounded-2xl transition-all duration-200 hover:shadow-lg flex items-center justify-center gap-2 text-sm mt-8">
                        <span>Confirmar Contraseña</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>
</html>
