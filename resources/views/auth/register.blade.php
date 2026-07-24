<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | Registro de Cliente</title>
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
                            /* blue-600 */
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
            <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Boutique Shopping">
            <div class="absolute inset-0 hero-overlay"></div>

            <div class="relative z-10 p-12">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="{{ asset('img/logo en blanco.png') }}" alt="Its Fashion Logo" class="w-12 h-auto object-contain">
                    <span class="font-serif text-2xl font-bold text-white tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
                </a>
            </div>

            <div class="relative z-10 p-12 mt-auto">
                <h2 class="text-4xl font-serif font-bold text-white mb-6 leading-tight">Sé parte de la<br>exclusividad</h2>
                <p class="text-gray-300 font-light text-lg leading-relaxed mb-8">
                    Crea tu cuenta para disfrutar de una experiencia de compra premium, acceso anticipado a colecciones y mucho más.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                            <i class="fas fa-gem text-brand-accent"></i>
                        </div>
                        <span class="font-light">Productos exclusivos y de calidad</span>
                    </div>
                    <div class="flex items-center gap-4 text-white/90">
                        <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                            <i class="fas fa-shield-alt text-brand-accent"></i>
                        </div>
                        <span class="font-light">Compras 100% seguras</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Register Form -->
        <div class="w-full md:w-7/12 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-white relative">
            <a href="{{ url('/') }}" class="md:hidden flex items-center gap-2 mb-8 text-brand-dark">
                <img src="{{ asset('img/logo en nombre.png') }}" alt="Its Fashion Logo" class="w-8 h-auto object-contain">
                <span class="font-serif text-xl font-bold tracking-wide">Its <span class="text-brand-accent">Fashion</span></span>
            </a>

            <div class="max-w-xl w-full mx-auto">
                <div class="mb-6">
                    <h1 class="text-3xl font-serif font-bold text-brand-dark tracking-tight mb-2">Crear Cuenta</h1>
                    <p class="text-brand-muted text-base font-light">Completa tus datos para unirte a Its Fashion</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-6" id="registro-form">
                    @csrf

                    <!-- Nombre / Apellido -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Nombre <span class="text-brand-accent">*</span></label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="100"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                placeholder="Ej. María">
                            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Apellido <span class="text-brand-accent">*</span></label>
                            <input type="text" name="apellido" value="{{ old('apellido') }}" required maxlength="100"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                placeholder="Ej. González">
                            @error('apellido') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-brand-dark mb-2">Nombre de usuario <span class="text-brand-accent">*</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input type="text" name="username" value="{{ old('username') }}" required maxlength="20"
                                class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                placeholder="Ej. Mi_username">
                        </div>
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email / Teléfono -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Correo Electrónico <span class="text-brand-accent">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                    <i class="fas fa-envelope text-sm"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required maxlength="100"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                    placeholder="correo@ejemplo.com">
                            </div>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Teléfono <span class="text-brand-accent">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                    <i class="fas fa-phone text-sm"></i>
                                </span>
                                <input type="text" name="telefono" value="{{ old('telefono') }}" required maxlength="15"
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                    placeholder="Ej. 3001234567">
                            </div>
                            @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Contraseña / Confirmar -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Contraseña <span class="text-brand-accent">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                    placeholder="••••••••">
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Confirmar Contraseña <span class="text-brand-accent">*</span></label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-brand-accent transition-colors pointer-events-none">
                                    <i class="fas fa-check-double text-sm"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="confirmar_password" required
                                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-brand-accent focus:bg-white transition-all text-brand-dark placeholder:text-gray-400 font-light"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full mt-4 bg-brand-dark text-white font-medium py-3 rounded-xl hover:bg-brand-accent transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg hover:shadow-brand-accent/30 focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 flex justify-center items-center gap-2">
                        <i class="fas fa-user-plus"></i> Registrarme
                    </button>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-sm text-brand-muted">
                        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-brand-accent font-medium hover:underline">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json($errors->first()),
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Aceptar',
                customClass: {
                    confirmButton: 'font-sans rounded-xl',
                    popup: 'font-sans rounded-3xl',
                    title: 'font-serif text-brand-dark',
                }
            });
        });
    </script>
    @endif

    <script>
        document.getElementById('registro-form').addEventListener('submit', function(e) {
            const pw = document.getElementById('password').value;
            const pw2 = document.getElementById('confirmar_password').value;
            if (pw !== pw2) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.',
                    confirmButtonColor: '#2563eb',
                    customClass: {
                        confirmButton: 'font-sans rounded-xl',
                        popup: 'font-sans rounded-3xl',
                        title: 'font-serif text-brand-dark',
                    }
                });
            }
        });
    </script>
</body>

</html>