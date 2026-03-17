<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Oficios y Recibos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Cultura</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('oficios.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Oficios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('recibos.index') }}">
                            <i class="bi bi-receipt"></i> Recibos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calendario') }}">
                            <i class="bi bi-calendar3"></i> Calendario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tiempo.index') }}">
                            <i class="bi bi-clock-history"></i> Tiempo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people"></i> Empleados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('almacen.index') }}">
                            <i class="bi bi-box-seam"></i> Almacén
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('entregas.index') }}">
                            <i class="bi bi-box-arrow-right"></i> Entregas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('asistencias.index') }}">
                            <i class="bi bi-person-check"></i> Asistencia
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>