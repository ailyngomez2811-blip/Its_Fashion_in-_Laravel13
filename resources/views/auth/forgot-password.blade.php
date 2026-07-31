<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recuperar contraseña en Its Fashion">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | Recuperar Contraseña</title>
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
                <h2 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">Recupera tu<br>acceso al sistema</h2>
                <p class="text-gray-300 font-light text-sm leading-relaxed mb-8">
                    Ingresa tu correo electrónico registrado y te enviaremos un enlace de recuperación para restablecer tu contraseña.
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
                    <h1 class="text-3xl font-serif font-bold text-brand-dark mb-2">¿Olvidaste tu contraseña?</h1>
                    <p class="text-sm text-brand-muted font-light leading-relaxed">
                        No hay problema. Indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecerla.
                    </p>
                </div>

                <!-- Session Status (Success Alert) -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-emerald-800">¡Enlace enviado!</p>
                            <p class="text-[11px] text-emerald-600 mt-0.5">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 pointer-events-none">
                                <i class="far fa-envelope text-sm"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-2xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-accent focus:bg-white transition-all duration-200 @error('email') border-red-300 focus:border-red-500 @enderror"
                                placeholder="tuemail@ejemplo.com">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-500 flex items-center gap-1.5 mt-1">
                                <i class="fas fa-exclamation-circle text-[10px]"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <button type="submit"
                        class="w-full py-4 bg-brand-dark hover:bg-slate-800 text-white font-bold rounded-2xl transition-all duration-200 hover:shadow-lg flex items-center justify-center gap-2 text-sm mt-8">
                        <span>Enviar enlace de recuperación</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-brand-muted hover:text-brand-accent transition-colors">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        <span>Volver al inicio de sesión</span>
                    </a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
