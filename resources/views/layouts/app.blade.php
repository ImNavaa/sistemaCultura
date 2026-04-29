<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Cultura')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --navy:    #1a1a2e;
            --navy2:   #16213e;
            --navy3:   #0f3460;
            --accent:  #e94560;
            --sidebar-w: 240px;
        }

        /* ── Base ── */
        body { background: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; }

        /* ── Navbar ── */
        .app-navbar {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy3) 100%);
            box-shadow: 0 2px 12px rgba(0,0,0,.35);
            padding: .45rem 0;
        }
        .app-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: .03em;
            color: #fff !important;
        }
        .app-navbar .navbar-brand .brand-icon {
            background: var(--accent);
            color: #fff;
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 1rem;
        }
        .app-navbar .nav-link {
            color: rgba(255,255,255,.78) !important;
            font-size: .875rem;
            padding: .4rem .75rem !important;
            border-radius: 6px;
            transition: all .18s;
        }
        .app-navbar .nav-link:hover,
        .app-navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,.12);
        }
        .app-navbar .dropdown-menu {
            background: var(--navy2);
            border: 1px solid rgba(255,255,255,.1);
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
            border-radius: 10px;
            overflow: hidden;
            min-width: 200px;
        }
        .app-navbar .dropdown-item {
            color: rgba(255,255,255,.8);
            font-size: .875rem;
            padding: .55rem 1rem;
            transition: background .15s;
        }
        .app-navbar .dropdown-item:hover { background: rgba(255,255,255,.1); color: #fff; }
        .app-navbar .dropdown-divider { border-color: rgba(255,255,255,.1); }
        .app-navbar .dropdown-item-text { color: rgba(255,255,255,.5); font-size: .8rem; padding: .5rem 1rem; }
        .app-navbar .dropdown-item.text-danger { color: #ff6b6b !important; }
        .app-navbar .badge-rol {
            font-size: .65rem;
            background: rgba(255,255,255,.15);
            color: #fff;
            border-radius: 4px;
            padding: 2px 6px;
        }

        /* ── Page wrapper ── */
        .page-wrapper { padding: 1.5rem 0 3rem; }

        /* ── Page header ── */
        .page-header {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .page-header-left { display: flex; align-items: center; gap: 14px; }
        .page-header-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .page-header-icon.navy  { background: linear-gradient(135deg, var(--navy), var(--navy3)); color: #fff; }
        .page-header-icon.green { background: linear-gradient(135deg, #1a7a4a, #27ae60); color: #fff; }
        .page-header-icon.blue  { background: linear-gradient(135deg, #1565c0, #1e88e5); color: #fff; }
        .page-header-icon.amber { background: linear-gradient(135deg, #e65100, #fb8c00); color: #fff; }
        .page-header-icon.teal  { background: linear-gradient(135deg, #00695c, #00897b); color: #fff; }
        .page-header-icon.purple{ background: linear-gradient(135deg, #4a148c, #7b1fa2); color: #fff; }
        .page-header-icon.red   { background: linear-gradient(135deg, #b71c1c, #e53935); color: #fff; }
        .page-header h2 { font-size: 1.35rem; font-weight: 700; margin: 0; color: var(--navy); }
        .page-header .breadcrumb { margin: 0; font-size: .8rem; }
        .page-header .breadcrumb-item a { color: var(--navy3); text-decoration: none; }
        .page-header .breadcrumb-item.active { color: #888; }

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 100%;
        }
        .stat-card-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-card-icon.green  { background: #e8f5e9; color: #2e7d32; }
        .stat-card-icon.blue   { background: #e3f2fd; color: #1565c0; }
        .stat-card-icon.amber  { background: #fff8e1; color: #e65100; }
        .stat-card-icon.red    { background: #ffebee; color: #c62828; }
        .stat-card-icon.teal   { background: #e0f2f1; color: #00695c; }
        .stat-card-icon.purple { background: #f3e5f5; color: #6a1b9a; }
        .stat-card-icon.navy   { background: #e8eaf6; color: var(--navy); }
        .stat-card-value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: #1a1a2e; }
        .stat-card-label { font-size: .8rem; color: #888; margin-top: 3px; }

        /* ── Data card ── */
        .data-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden;
        }
        .data-card-header {
            padding: .85rem 1.25rem;
            font-weight: 600;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
            color: var(--navy);
        }
        .data-card-header .header-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
        }
        .data-card-header .header-icon.navy   { background: var(--navy); color: #fff; }
        .data-card-header .header-icon.green  { background: #2e7d32; color: #fff; }
        .data-card-header .header-icon.blue   { background: #1565c0; color: #fff; }
        .data-card-header .header-icon.amber  { background: #e65100; color: #fff; }
        .data-card-header .header-icon.teal   { background: #00695c; color: #fff; }

        /* ── Tables ── */
        .data-card .table { margin-bottom: 0; }
        .data-card .table thead th {
            background: var(--navy);
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: none;
            padding: .75rem 1rem;
            white-space: nowrap;
        }
        .data-card .table tbody td {
            padding: .75rem 1rem;
            vertical-align: middle;
            font-size: .9rem;
            border-color: #f0f2f5;
        }
        .data-card .table tbody tr:hover { background: #f8f9fc; }
        .data-card .table tbody tr:last-child td { border-bottom: none; }

        /* ── Form card ── */
        .form-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden;
        }
        .form-card-header {
            background: linear-gradient(135deg, var(--navy), var(--navy3));
            color: #fff;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: .95rem;
        }
        .form-card-body { padding: 1.5rem; }
        .form-section-title {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #888;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
            margin-bottom: 1rem;
        }
        .form-label { font-size: .875rem; font-weight: 500; color: #444; margin-bottom: .35rem; }
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #dee2e6;
            font-size: .9rem;
            transition: border-color .18s, box-shadow .18s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--navy3);
            box-shadow: 0 0 0 3px rgba(15,52,96,.12);
        }
        .form-control[readonly] { background: #f8f9fc; color: #666; }

        /* ── Buttons ── */
        .btn { border-radius: 8px; font-size: .875rem; font-weight: 500; transition: all .18s; }
        .btn-navy {
            background: linear-gradient(135deg, var(--navy), var(--navy3));
            color: #fff; border: none;
        }
        .btn-navy:hover { opacity: .9; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(15,52,96,.35); }
        .btn-sm { border-radius: 6px; font-size: .8rem; }
        .btn-action {
            width: 30px; height: 30px;
            border-radius: 6px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .9rem; padding: 0;
        }

        /* ── Badges ── */
        .badge { border-radius: 6px; font-weight: 500; }
        .badge-navy { background: var(--navy); color: #fff; }

        /* ── Stock badge ── */
        .stock-ok   { background: #e8f5e9; color: #2e7d32; border-radius: 6px; padding: 3px 10px; font-weight: 600; font-size: .85rem; }
        .stock-warn { background: #fff8e1; color: #e65100; border-radius: 6px; padding: 3px 10px; font-weight: 600; font-size: .85rem; }
        .stock-low  { background: #ffebee; color: #c62828; border-radius: 6px; padding: 3px 10px; font-weight: 600; font-size: .85rem; }

        /* ── Alerts ── */
        .alert { border-radius: 10px; border: none; font-size: .9rem; }
        .alert-success { background: #e8f5e9; color: #1b5e20; }
        .alert-danger  { background: #ffebee; color: #b71c1c; }

        /* ── Profile info list ── */
        .info-list { list-style: none; padding: 0; margin: 0; }
        .info-list li {
            display: flex;
            gap: 1rem;
            padding: .65rem 0;
            border-bottom: 1px solid #f0f2f5;
            font-size: .9rem;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-list .info-key { color: #888; min-width: 130px; font-size: .8rem; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; padding-top: 2px; }
        .info-list .info-val { color: #1a1a2e; font-weight: 500; }

        /* ── Días laborales chips ── */
        .day-chip {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
            background: #e8eaf6;
            color: var(--navy);
            margin: 2px;
        }
        .day-chip.today { background: var(--navy3); color: #fff; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #bbb;
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }
        .empty-state p { font-size: .9rem; margin: 0; }

        /* ── Permissions matrix ── */
        .perm-table thead th { background: var(--navy); color: #fff; font-size: .78rem; letter-spacing: .05em; padding: .65rem .75rem; }
        .perm-table tbody td { padding: .6rem .75rem; vertical-align: middle; border-color: #f0f2f5; }
        .perm-table .form-check-input { width: 1.1em; height: 1.1em; cursor: pointer; }
        .perm-table .form-check-input:checked { background-color: var(--navy3); border-color: var(--navy3); }

        /* ── Pagination ── */
        .pagination { gap: 3px; margin-bottom: 0; flex-wrap: wrap; justify-content: center; }
        .page-link {
            border-radius: 7px !important;
            padding: .28rem .62rem;
            font-size: .82rem;
            color: var(--navy3);
            border-color: #e0e3e8;
            min-width: 32px;
            text-align: center;
            line-height: 1.6;
        }
        .page-item.active .page-link { background: var(--navy3); border-color: var(--navy3); color: #fff; }
        .page-item.disabled .page-link { color: #ccc; background: #fafafa; }
        .page-link:hover { background: #e8eaf6; color: var(--navy); border-color: #c5cae9; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f0f2f5; }
        ::-webkit-scrollbar-thumb { background: #c0c4cc; border-radius: 3px; }
    </style>
    @stack('styles')
</head>

<body>

    <nav class="app-navbar navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('inicio') }}">
                <span class="brand-icon"><i class="bi bi-building"></i></span>
                Cultura
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    style="color:rgba(255,255,255,.7)">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto gap-1">

                    @if(auth()->user()?->puede('calendario', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('calendario') ? 'active' : '' }}" href="{{ route('calendario') }}">
                            <i class="bi bi-calendar3 me-1"></i>Calendario
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('oficios', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('oficios*') ? 'active' : '' }}" href="{{ route('oficios.index') }}">
                            <i class="bi bi-file-earmark-text me-1"></i>Oficios
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('recibos', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('recibos*') ? 'active' : '' }}" href="{{ route('recibos.index') }}">
                            <i class="bi bi-receipt me-1"></i>Recibos
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('asistencias', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('asistencias*') ? 'active' : '' }}" href="{{ route('asistencias.index') }}">
                            <i class="bi bi-person-check me-1"></i>Asistencia
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('usuarios', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people me-1"></i>Empleados
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('almacen', 'ver'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('almacen*') || request()->is('entregas*') ? 'active' : '' }}"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-box-seam me-1"></i>Almacén
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('almacen.index') }}">
                                    <i class="bi bi-box-seam me-2"></i>Inventario
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('entregas.index') }}">
                                    <i class="bi bi-box-arrow-right me-2"></i>Vales de Salida
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('tiempo', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tiempo*') ? 'active' : '' }}" href="{{ route('tiempo.index') }}">
                            <i class="bi bi-clock-history me-1"></i>Tiempo
                        </a>
                    </li>
                    @endif

                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <span style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="d-none d-lg-inline" style="font-size:.875rem;color:rgba(255,255,255,.9);">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->rol)
                            <span class="badge-rol d-none d-lg-inline">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->rol->nombre)) }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text">{{ auth()->user()->email }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
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

    <div class="container page-wrapper">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

</body>
</html>
