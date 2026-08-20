<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | @yield('titulo', 'Panel Empleado')</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('img/icono head .png') }}" type="image/png">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Tailwind para el contenido -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        /* ===== Reskin de AdminLTE con la marca Its Fashion ===== */
        .main-sidebar {
            background-color: #0f172a !important;
        }

        .brand-link {
            background-color: #0f172a !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .brand-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .nav-sidebar>.nav-item>.nav-link.active,
        .nav-sidebar .nav-treeview>.nav-item>.nav-link.active {
            background-color: rgba(37, 99, 235, 0.15) !important;
            color: #60a5fa !important;
            border-right: 3px solid #2563eb;
        }

        .nav-sidebar .nav-link {
            color: #94a3b8;
        }

        .nav-sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-sidebar .nav-header {
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .main-header.navbar {
            background-color: #fff !important;
            border-bottom: 1px solid #e2e8f0;
        }

        .content-wrapper {
            background-color: #f8fafc;
        }
    </style>

    @stack('estilos')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- NAVBAR -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto flex items-center gap-3">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown relative" id="bell-dropdown-container">
                    <a class="nav-link relative cursor-pointer flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 transition" data-toggle="dropdown" href="#" id="bell-icon-link">
                        <i class="far fa-bell text-slate-600 text-sm"></i>
                        <span id="bell-badge" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[9px] font-bold">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right rounded-2xl border border-slate-100 shadow-xl p-0 overflow-hidden" style="min-width: 320px;">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Notificaciones</span>
                            <button onclick="leerTodasNotificaciones(event)" class="text-[10px] text-blue-600 hover:underline font-semibold">Marcar como leídas</button>
                        </div>
                        <div id="notifications-list" class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            <div class="py-8 text-center text-slate-400">
                                <i class="far fa-bell-slash text-2xl mb-2 opacity-30"></i>
                                <p class="text-xs">No tienes notificaciones nuevas</p>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-toggle="dropdown" href="#">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
                              style="width:32px;height:32px;font-size:13px;">
                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}
                        </span>
                        {{ auth()->user()->nombre }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form-emp').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                        </a>
                        <form id="logout-form-emp" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- SIDEBAR -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ asset('img/logo en blanco.png') }}" alt="Its Fashion" class="brand-image" style="opacity:1; max-height:33px; width:auto;">
                <span class="brand-text">Its <span class="text-primary">Fashion</span></span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        <li class="nav-header">Principal</li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th-large"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">Operaciones</li>
                        <li class="nav-item">
                            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>Ventas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('compras.index') }}" class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>Abastecimiento</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>Inventario</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('devoluciones.index') }}" class="nav-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-undo-alt"></i>
                                <p>Devoluciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('caja.index') }}" class="nav-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>Caja</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck-loading"></i>
                                <p>Proveedores</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTENT -->
        <div class="content-wrapper">
            @yield('content')
        </div>

    </div>

    <!-- AdminLTE scripts -->
    <script src="{{ asset('AdminLTE-3.2.0/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.2.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.2.0/dist/js/adminlte.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de Notificaciones -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            cargarNotificaciones();
            setInterval(cargarNotificaciones, 30000);
        });

        function cargarNotificaciones() {
            fetch("{{ route('notificaciones.index') }}", {
                headers: { 'Accept': 'application/json' }
            })
                .then(r => {
                    if (r.status === 401) return [];
                    return r.json();
                })
                .then(notis => {
                    const badge = document.getElementById('bell-badge');
                    const list = document.getElementById('notifications-list');
                    if (notis.length > 0) {
                        badge.textContent = notis.length;
                        badge.classList.remove('hidden');

                        list.innerHTML = notis.map(n => {
                            let icon = 'fa-info-circle text-blue-500';
                            let bg = 'bg-blue-50';
                            if (n.data.type === 'devolucion') {
                                icon = 'fa-undo-alt text-amber-500';
                                bg = 'bg-amber-50';
                            } else if (n.data.type === 'stock_critico') {
                                icon = 'fa-exclamation-triangle text-red-500';
                                bg = 'bg-red-50';
                            }

                            return `
                                <a href="${n.data.url}" class="flex items-start gap-3 p-3 hover:bg-slate-50 transition-colors">
                                    <div class="w-8 h-8 rounded-lg ${bg} flex items-center justify-center flex-shrink-0">
                                        <i class="fas ${icon} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">${n.data.title}</p>
                                        <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">${n.data.message}</p>
                                    </div>
                                </a>
                            `;
                        }).join('');
                    } else {
                        badge.classList.add('hidden');
                        list.innerHTML = `
                            <div class="py-8 text-center text-slate-400">
                                <i class="far fa-bell-slash text-2xl mb-2 opacity-30"></i>
                                <p class="text-xs">No tienes notificaciones nuevas</p>
                            </div>
                        `;
                    }
                });
        }

        function leerTodasNotificaciones(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            fetch("{{ route('notificaciones.readAll') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    cargarNotificaciones();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
