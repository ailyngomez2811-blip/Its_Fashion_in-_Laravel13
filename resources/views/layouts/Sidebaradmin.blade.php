<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Its Fashion | @yield('titulo', 'Panel Administrador')</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('img/icono head .png') }}" type="image/png">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.2.0/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">

    <!-- Tailwind, para el contenido de cada vista -->
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

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-toggle="dropdown" href="#">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width:32px;height:32px;font-size:13px;">
                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}
                        </span>
                        {{ auth()->user()->nombre }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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

                        @if (auth()->user()->rol_id === 1)
                        <li class="nav-header">Gestión</li>
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Productos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Categorías</p>
                            </a>
                        </li>
                        @endif

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
                            <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck-loading"></i>
                                <p>Proveedores</p>
                            </a>
                        </li>

                        @if (auth()->user()->rol_id === 1)
                        <li class="nav-item">
                            <a href="{{ route('caja.index') }}" class="nav-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>Caja</p>
                            </a>
                        </li>
                        <li class="nav-header">Análisis</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Reportes</p>
                            </a>
                        </li>
                        @endif

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

    @stack('scripts')
</body>

</html>