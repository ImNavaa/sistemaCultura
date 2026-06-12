<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Registro de Actividades') — Cultura</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --navy: #1a237e;
            --navy2: #283593;
        }
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .pub-header {
            background: linear-gradient(135deg, var(--navy), var(--navy2));
            color: #fff;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .pub-header .logo-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .pub-header h1 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }
        .pub-header .sub {
            font-size: .78rem;
            opacity: .8;
        }
        .pub-body {
            max-width: 960px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .pub-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            padding: 2rem;
        }
        .pub-footer {
            text-align: center;
            color: #94a3b8;
            font-size: .78rem;
            padding: 2rem 1rem;
        }
    </style>
    @stack('styles')
</head>
<body>
<header class="pub-header">
    <div class="logo-icon"><i class="bi bi-building"></i></div>
    <div>
        <h1>Dirección de Cultura</h1>
        <div class="sub">Sistema de Registro de Actividades</div>
    </div>
</header>

<div class="pub-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible mb-3" role="alert">
            <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            @foreach($errors->all() as $e)
                <div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</div>

<footer class="pub-footer">
    Dirección de Cultura &copy; {{ date('Y') }}
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
