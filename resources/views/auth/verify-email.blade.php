<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verificación de correo en Its Fashion">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | Verificar Correo</title>
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
                <h2 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">Verifica tu<br>cuenta de usuario</h2>
                <p class="text-gray-300 font-light text-sm leading-relaxed mb-8">
                    Confirma tu dirección de correo electrónico para asegurar que eres tú y tener acceso completo a la boutique.
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
                    <h1 class="text-3xl font-serif font-bold text-brand-dark mb-2">Verifica tu correo</h1>
                    <p class="text-sm text-brand-muted font-light leading-relaxed">
                        ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo haciendo clic en el enlace que te acabamos de enviar? Si no lo recibiste, con gusto te enviaremos otro.
                    </p>
                </div>

                <!-- Session Status (Success Alert) -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-emerald-800">¡Enlace enviado!</p>
                            <p class="text-[11px] text-emerald-600 mt-0.5">Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada.</p>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row items-center gap-4 mt-8">
                    <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full py-3.5 bg-brand-dark hover:bg-slate-800 text-white font-bold rounded-2xl transition-all duration-200 hover:shadow-lg flex items-center justify-center gap-2 text-xs">
                            <i class="far fa-paper-plane text-xs"></i>
                            <span>Reenviar correo de verificación</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                            class="w-full px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition-all duration-200 text-xs">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
