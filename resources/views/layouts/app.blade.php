<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Cultura')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('calendario') }}">
                <i class="bi bi-building"></i> Cultura
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                    @if(auth()->user()?->puede('calendario', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('calendario') ? 'active' : '' }}"
                            href="{{ route('calendario') }}">
                            <i class="bi bi-calendar3"></i> Calendario
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('oficios', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('oficios*') ? 'active' : '' }}"
                            href="{{ route('oficios.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Oficios
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('recibos', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('recibos*') ? 'active' : '' }}"
                            href="{{ route('recibos.index') }}">
                            <i class="bi bi-receipt"></i> Recibos
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('asistencias', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('asistencias*') ? 'active' : '' }}"
                            href="{{ route('asistencias.index') }}">
                            <i class="bi bi-person-check"></i> Asistencia
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('usuarios', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}"
                            href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people"></i> Empleados
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('almacen', 'ver'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('almacen*') || request()->is('entregas*') ? 'active' : '' }}"
                            href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-box-seam"></i> Almacén
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li>
                                <a class="dropdown-item" href="{{ route('almacen.index') }}">
                                    <i class="bi bi-box-seam"></i> Inventario
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('entregas.index') }}">
                                    <i class="bi bi-box-arrow-right"></i> Entregas
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('tiempo', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tiempo*') ? 'active' : '' }}"
                            href="{{ route('tiempo.index') }}">
                            <i class="bi bi-clock-history"></i> Tiempo
                        </a>
                    </li>
                    @endif

                </ul>

                {{-- Usuario y logout --}}
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                            href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->rol)
                            <span class="badge bg-secondary" style="font-size:0.65rem">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->rol->nombre)) }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                            <li>
                                <span class="dropdown-item-text text-muted small">
                                    {{ auth()->user()->email }}
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>

            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

</body>

</html>