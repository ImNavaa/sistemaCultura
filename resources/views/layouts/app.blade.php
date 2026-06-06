<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Cultura')</title>
    {{-- Aplicar tema antes de que carguen los estilos para evitar parpadeo --}}
    <script>(function(){const t=localStorage.getItem('tema');if(t==='oscuro')document.documentElement.setAttribute('data-theme','dark');})()</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --navy:    #1a1a2e;
            --navy2:   #16213e;
            --navy3:   #0f3460;
            --accent:  #e94560;
            --sidebar-w: 240px;
            /* Colores semánticos (modo claro) */
            --bg-body:       #f0f2f5;
            --bg-card:       #ffffff;
            --bg-card-alt:   #fafafa;
            --bg-input:      #ffffff;
            --bg-input-ro:   #f8f9fc;
            --bg-row-hover:  #f8f9fc;
            --border-color:  #f0f0f0;
            --border-light:  #f0f2f5;
            --text-main:     #1a1a2e;
            --text-muted:    #888888;
            --text-label:    #444444;
            --text-body:     inherit;
        }

        /* ── Modo oscuro ── */
        [data-theme="dark"] {
            --bg-body:       #0f1117;
            --bg-card:       #1e1e2e;
            --bg-card-alt:   #16162a;
            --bg-input:      #252538;
            --bg-input-ro:   #1a1a2e;
            --bg-row-hover:  #252538;
            --border-color:  #2a2a40;
            --border-light:  #2a2a40;
            --text-main:     #e0e0e0;
            --text-muted:    #888888;
            --text-label:    #bbbbbb;
            --text-body:     #d0d0d0;
        }

        /* ── Base ── */
        body { background: var(--bg-body); color: var(--text-body); font-family: 'Segoe UI', system-ui, sans-serif; transition: background .2s, color .2s; }

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
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: background .2s;
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
        .page-header h2 { font-size: 1.35rem; font-weight: 700; margin: 0; color: var(--text-main); }
        .page-header .breadcrumb { margin: 0; font-size: .8rem; }
        .page-header .breadcrumb-item a { color: var(--navy3); text-decoration: none; }
        .page-header .breadcrumb-item.active { color: var(--text-muted); }

        /* ── Stat cards ── */
        .stat-card {
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 100%;
            transition: background .2s;
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
        .stat-card-value { font-size: 1.75rem; font-weight: 700; line-height: 1; color: var(--text-main); }
        .stat-card-label { font-size: .8rem; color: var(--text-muted); margin-top: 3px; }

        /* ── Data card ── */
        .data-card {
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden;
            transition: background .2s;
        }
        .data-card-header {
            padding: .85rem 1.25rem;
            font-weight: 600;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-card-alt);
            color: var(--text-main);
            transition: background .2s, border-color .2s;
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
            border-color: var(--border-light);
            color: var(--text-body);
        }
        .data-card .table tbody tr:hover { background: var(--bg-row-hover); }
        .data-card .table tbody tr:last-child td { border-bottom: none; }

        /* ── Form card ── */
        .form-card {
            background: var(--bg-card);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden;
            transition: background .2s;
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
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 6px;
            margin-bottom: 1rem;
        }
        .form-label { font-size: .875rem; font-weight: 500; color: var(--text-label); margin-bottom: .35rem; }
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #dee2e6;
            font-size: .9rem;
            background: var(--bg-input);
            color: var(--text-body);
            transition: border-color .18s, box-shadow .18s, background .2s, color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--navy3);
            box-shadow: 0 0 0 3px rgba(15,52,96,.12);
            background: var(--bg-input);
            color: var(--text-body);
        }
        .form-control[readonly] { background: var(--bg-input-ro); color: var(--text-muted); }

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
            border-bottom: 1px solid var(--border-light);
            font-size: .9rem;
        }
        .info-list li:last-child { border-bottom: none; }
        .info-list .info-key { color: var(--text-muted); min-width: 130px; font-size: .8rem; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; padding-top: 2px; }
        .info-list .info-val { color: var(--text-main); font-weight: 500; }

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
            color: var(--text-muted);
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
            border-color: var(--border-light);
            background: var(--bg-card);
            min-width: 32px;
            text-align: center;
            line-height: 1.6;
        }
        .page-item.active .page-link { background: var(--navy3); border-color: var(--navy3); color: #fff; }
        .page-item.disabled .page-link { color: var(--text-muted); background: var(--bg-card-alt); }
        .page-link:hover { background: var(--bg-row-hover); color: var(--text-main); border-color: var(--border-color); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #c0c4cc; border-radius: 3px; }

        /* ── Modo oscuro: overrides específicos ── */
        [data-theme="dark"] .stat-card-icon.green  { background: #1a3a2e; color: #81c784; }
        [data-theme="dark"] .stat-card-icon.blue   { background: #1a2a3e; color: #90caf9; }
        [data-theme="dark"] .stat-card-icon.amber  { background: #3a2a10; color: #ffb74d; }
        [data-theme="dark"] .stat-card-icon.red    { background: #3a1a1a; color: #ef9a9a; }
        [data-theme="dark"] .stat-card-icon.teal   { background: #0d2e2a; color: #80cbc4; }
        [data-theme="dark"] .stat-card-icon.purple { background: #2a1a3e; color: #ce93d8; }
        [data-theme="dark"] .stat-card-icon.navy   { background: #1a1e3e; color: #9fa8da; }
        [data-theme="dark"] .day-chip              { background: #2a2a45; color: #c5cae9; }
        [data-theme="dark"] .stock-ok              { background: #1a3a2e; color: #81c784; }
        [data-theme="dark"] .stock-warn            { background: #3a2a10; color: #ffb74d; }
        [data-theme="dark"] .stock-low             { background: #3a1a1a; color: #ef9a9a; }
        [data-theme="dark"] .alert-success         { background: #1a3a2e; color: #a5d6a7; }
        [data-theme="dark"] .alert-danger          { background: #3a1a1a; color: #ef9a9a; }
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select           { border-color: #3a3a55; }
        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus     { border-color: #5c6bc0; box-shadow: 0 0 0 3px rgba(92,107,192,.2); }
        [data-theme="dark"] .form-control::placeholder { color: #555; }
        [data-theme="dark"] .form-select option    { background: #252538; }
        [data-theme="dark"] .perm-table thead th   { background: #0f1117; }
        [data-theme="dark"] .perm-table tbody td   { border-color: #2a2a40; }
        [data-theme="dark"] .modal-content         { background: #1e1e2e; color: var(--text-body); }
        [data-theme="dark"] .modal-header          { border-bottom-color: #2a2a40; }
        [data-theme="dark"] .modal-footer          { border-top-color: #2a2a40; }
        [data-theme="dark"] .btn-close             { filter: invert(1) grayscale(1); }
        [data-theme="dark"] .text-muted            { color: #777 !important; }
        [data-theme="dark"] hr                     { border-color: #2a2a40; }
        [data-theme="dark"] .border                { border-color: #2a2a40 !important; }
        [data-theme="dark"] ::-webkit-scrollbar-thumb { background: #3a3a55; }

        /* ── Modo oscuro: Bootstrap genérico ── */
        [data-theme="dark"] .card                    { background: var(--bg-card); border-color: var(--border-color); color: var(--text-body); }
        [data-theme="dark"] .card-header            { background: var(--bg-card-alt); border-color: var(--border-color); color: var(--text-main); }
        [data-theme="dark"] .card-body              { background: var(--bg-card); }
        [data-theme="dark"] .card-footer            { background: var(--bg-card-alt); border-color: var(--border-color); }
        [data-theme="dark"] .bg-white               { background-color: var(--bg-card) !important; }
        [data-theme="dark"] .bg-light               { background-color: var(--bg-card-alt) !important; }
        [data-theme="dark"] .bg-body                { background-color: var(--bg-body) !important; }
        [data-theme="dark"] .text-dark              { color: var(--text-main) !important; }
        [data-theme="dark"] .text-body              { color: var(--text-body) !important; }
        [data-theme="dark"] .text-muted             { color: #777 !important; }
        [data-theme="dark"] .border,
        [data-theme="dark"] .border-top,
        [data-theme="dark"] .border-bottom,
        [data-theme="dark"] .border-start,
        [data-theme="dark"] .border-end             { border-color: var(--border-color) !important; }
        [data-theme="dark"] hr                      { border-color: var(--border-color); opacity: 1; }
        /* Tablas Bootstrap genéricas */
        [data-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(255,255,255,.03);
            --bs-table-hover-bg: var(--bg-row-hover);
            --bs-table-border-color: var(--border-color);
            color: var(--text-body);
        }
        [data-theme="dark"] .table > :not(caption) > * { border-color: var(--border-color); }
        [data-theme="dark"] .table > :not(caption) > * > * { background-color: transparent; color: var(--text-body); }
        [data-theme="dark"] thead th                { background: #0f1117 !important; color: #fff !important; }
        /* Inputs y selects de Bootstrap */
        [data-theme="dark"] .input-group-text       { background: var(--bg-card-alt); border-color: #3a3a55; color: var(--text-body); }
        [data-theme="dark"] .form-check-label       { color: var(--text-body); }
        [data-theme="dark"] .form-text              { color: #777; }
        /* List groups */
        [data-theme="dark"] .list-group-item        { background: var(--bg-card); border-color: var(--border-color); color: var(--text-body); }
        [data-theme="dark"] .list-group-item:hover  { background: var(--bg-row-hover); }
        /* Navs y tabs */
        [data-theme="dark"] .nav-tabs               { border-color: var(--border-color); }
        [data-theme="dark"] .nav-tabs .nav-link     { color: var(--text-muted); }
        [data-theme="dark"] .nav-tabs .nav-link.active { background: var(--bg-card); border-color: var(--border-color); color: var(--text-main); }
        [data-theme="dark"] .nav-pills .nav-link    { color: var(--text-muted); }
        [data-theme="dark"] .nav-pills .nav-link.active { background: var(--navy3); color: #fff; }
        /* Alertas Bootstrap */
        [data-theme="dark"] .alert-warning          { background: #3a2a10; color: #ffb74d; border-color: #5a3f15; }
        [data-theme="dark"] .alert-info             { background: #0d2a3e; color: #90caf9; border-color: #1a4a6a; }
        [data-theme="dark"] .alert-primary          { background: #1a1e3e; color: #9fa8da; border-color: #2a2e5e; }
        [data-theme="dark"] .alert-secondary        { background: #2a2a3e; color: #bbb; border-color: #3a3a55; }
        /* Badges Bootstrap */
        [data-theme="dark"] .badge.bg-secondary     { background-color: #3a3a55 !important; }
        [data-theme="dark"] .badge.bg-light         { background-color: #2a2a45 !important; color: #c5cae9 !important; }
        /* Accordion */
        [data-theme="dark"] .accordion-item         { background: var(--bg-card); border-color: var(--border-color); }
        [data-theme="dark"] .accordion-button       { background: var(--bg-card-alt); color: var(--text-main); }
        [data-theme="dark"] .accordion-button:not(.collapsed) { background: var(--bg-card); color: var(--text-main); }
        [data-theme="dark"] .accordion-body         { background: var(--bg-card); color: var(--text-body); }
        /* Offcanvas */
        [data-theme="dark"] .offcanvas              { background: var(--bg-card); color: var(--text-body); }
        [data-theme="dark"] .offcanvas-header       { border-color: var(--border-color); }
        /* Tooltips y popovers */
        [data-theme="dark"] .popover                { background: var(--bg-card); border-color: var(--border-color); }
        [data-theme="dark"] .popover-header         { background: var(--bg-card-alt); border-color: var(--border-color); color: var(--text-main); }
        [data-theme="dark"] .popover-body           { color: var(--text-body); }
        /* Breadcrumb */
        [data-theme="dark"] .breadcrumb-item a      { color: #90caf9; }
        [data-theme="dark"] .breadcrumb-item.active { color: var(--text-muted); }
        [data-theme="dark"] .breadcrumb-item + .breadcrumb-item::before { color: var(--text-muted); }
        /* Botones outline */
        [data-theme="dark"] .btn-outline-secondary  { color: #aaa; border-color: #3a3a55; }
        [data-theme="dark"] .btn-outline-secondary:hover { background: #2a2a45; color: #fff; border-color: #5c6bc0; }
        [data-theme="dark"] .btn-outline-primary    { color: #90caf9; border-color: #5c6bc0; }
        [data-theme="dark"] .btn-outline-primary:hover { background: #1a2a4e; color: #fff; }
        [data-theme="dark"] .btn-outline-danger     { color: #ef9a9a; border-color: #b71c1c; }
        [data-theme="dark"] .btn-outline-danger:hover { background: #3a1a1a; color: #fff; }
        [data-theme="dark"] .btn-outline-success    { color: #81c784; border-color: #2e7d32; }
        [data-theme="dark"] .btn-outline-success:hover { background: #1a3a2e; color: #fff; }
        [data-theme="dark"] .btn-light              { background: #2a2a45; color: var(--text-body); border-color: #3a3a55; }
        [data-theme="dark"] .btn-light:hover        { background: #333355; }
        /* Inputs checkbox/radio */
        [data-theme="dark"] .form-check-input       { background-color: var(--bg-input); border-color: #3a3a55; }
        [data-theme="dark"] .form-check-input:checked { background-color: var(--navy3); border-color: var(--navy3); }
        /* Paginación */
        [data-theme="dark"] .page-link              { background: var(--bg-card); }
        /* Spinner */
        [data-theme="dark"] .spinner-border         { color: #5c6bc0; }
        /* Colores de texto de Bootstrap */
        [data-theme="dark"] .text-secondary         { color: #777 !important; }
        [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3,
        [data-theme="dark"] h4, [data-theme="dark"] h5, [data-theme="dark"] h6 { color: var(--text-main); }
        [data-theme="dark"] p, [data-theme="dark"] span, [data-theme="dark"] label,
        [data-theme="dark"] small, [data-theme="dark"] td, [data-theme="dark"] th { color: inherit; }

        /* ── Toggle de tema ── */
        .btn-tema {
            width: 34px; height: 34px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,.25);
            background: rgba(255,255,255,.08);
            color: rgba(255,255,255,.85);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all .2s;
            padding: 0;
        }
        .btn-tema:hover { background: rgba(255,255,255,.18); color: #fff; border-color: rgba(255,255,255,.5); }
    </style>

    <style>
    /* ── List toolbar (vista + ordenamiento) ── */
    .list-toolbar {
        display: flex;
        align-items: center;
        gap: .65rem;
        flex-wrap: wrap;
        padding: .55rem 1rem;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-color);
        border-radius: 0;
    }
    .list-toolbar .sep {
        width: 1px; height: 20px;
        background: var(--border-color);
        flex-shrink: 0;
    }
    .btn-vista {
        width: 30px; height: 30px;
        border-radius: 7px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-muted);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .82rem; cursor: pointer;
        transition: all .15s;
        padding: 0;
    }
    .btn-vista:hover:not(.active) { background: var(--bg-card-alt); color: var(--text-main); }
    .btn-vista.active { background: var(--accent, #3a7bd5); border-color: var(--accent, #3a7bd5); color: #fff; }

    .sort-select { font-size: .78rem !important; padding: .25rem .5rem !important; height: 30px; }
    .btn-sortdir {
        width: 30px; height: 30px;
        border-radius: 7px;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-muted);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .85rem; cursor: pointer; padding: 0;
        transition: all .15s;
    }
    .btn-sortdir:hover { background: var(--bg-card-alt); color: var(--text-main); }

    /* ── Grid de tarjetas ── */
    .view-tarjetas { display: none; }
    .view-tarjetas.activo { display: grid; }
    .view-tabla.oculto { display: none; }

    .grid-cards {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: .75rem;
        padding: 1rem;
    }
    .list-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: .85rem 1rem;
        transition: box-shadow .15s, transform .1s;
        display: flex; flex-direction: column; gap: .4rem;
    }
    .list-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.1); transform: translateY(-1px); }
    .list-card .card-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); }
    .list-card .card-title { font-weight: 600; font-size: .9rem; line-height: 1.3; }
    .list-card .card-meta  { font-size: .78rem; color: var(--text-muted); display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
    .list-card .card-actions { display: flex; gap: .35rem; margin-top: .35rem; padding-top: .5rem; border-top: 1px solid var(--border-color); }
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

                    @if(auth()->user()?->puede('calendario', 'ver') || auth()->user()?->puede('agora', 'ver'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('calendario') || request()->is('agora*') ? 'active' : '' }}"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar3 me-1"></i>Calendarios
                        </a>
                        <ul class="dropdown-menu">
                            @if(auth()->user()?->puede('calendario', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('calendario') ? 'active' : '' }}" href="{{ route('calendario') }}">
                                    <i class="bi bi-calendar3 me-2"></i>Teatro
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()?->puede('agora', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('agora*') ? 'active' : '' }}" href="{{ route('agora.index') }}">
                                    <i class="bi bi-building me-2"></i>Ágora
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('oficios', 'ver') || auth()->user()?->puede('recibos', 'ver'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('oficios*') || request()->is('recibos*') ? 'active' : '' }}"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text me-1"></i>Documentos
                        </a>
                        <ul class="dropdown-menu">
                            @if(auth()->user()?->puede('oficios', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('oficios*') ? 'active' : '' }}" href="{{ route('oficios.index') }}">
                                    <i class="bi bi-file-earmark-text me-2"></i>Oficios
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()?->puede('recibos', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('recibos*') ? 'active' : '' }}" href="{{ route('recibos.index') }}">
                                    <i class="bi bi-receipt me-2"></i>Recibos
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(auth()->user()?->puede('asistencias', 'ver') || auth()->user()?->puede('usuarios', 'ver') || auth()->user()?->puede('tiempo', 'ver'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('asistencias*') || request()->is('usuarios*') || request()->is('tiempo*') ? 'active' : '' }}"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i>Personal
                        </a>
                        <ul class="dropdown-menu">
                            @if(auth()->user()?->puede('asistencias', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('asistencias*') ? 'active' : '' }}" href="{{ route('asistencias.index') }}">
                                    <i class="bi bi-person-check me-2"></i>Asistencia
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()?->puede('usuarios', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('usuarios*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                    <i class="bi bi-people me-2"></i>Empleados
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()?->puede('tiempo', 'ver'))
                            <li>
                                <a class="dropdown-item {{ request()->is('tiempo*') ? 'active' : '' }}" href="{{ route('tiempo.index') }}">
                                    <i class="bi bi-clock-history me-2"></i>Tiempo
                                </a>
                            </li>
                            @endif
                        </ul>
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

                    @if(auth()->user()?->puede('proyectos', 'ver'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('proyectos*') ? 'active' : '' }}"
                           href="{{ route('proyectos.index') }}">
                            <i class="bi bi-kanban me-1"></i>Proyectos
                        </a>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('herramientas*') ? 'active' : '' }}"
                           href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-tools me-1"></i>Herramientas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->is('herramientas/img-pdf') ? 'active' : '' }}"
                                   href="{{ route('herramientas.img-pdf') }}">
                                    <i class="bi bi-file-image me-2"></i>IMG a PDF
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item d-flex align-items-center">
                        <button class="btn-tema" id="btnTema" title="Cambiar tema" aria-label="Cambiar tema claro/oscuro">
                            <i class="bi bi-moon-fill" id="iconTema"></i>
                        </button>
                    </li>
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <span style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="d-none d-lg-inline" style="font-size:.875rem;color:rgba(255,255,255,.9);"
                                  title="{{ auth()->user()->name }}">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: auth()->user()->name }}</span>
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
                                <a class="dropdown-item {{ request()->is('configuracion') ? 'active' : '' }}"
                                   href="{{ route('configuracion') }}">
                                    <i class="bi bi-gear me-2"></i>Configuración
                                </a>
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

    <script>
    (function () {
        const btn  = document.getElementById('btnTema');
        const icon = document.getElementById('iconTema');
        const html = document.documentElement;

        function aplicarTema(oscuro) {
            if (oscuro) {
                html.setAttribute('data-theme', 'dark');
                icon.className = 'bi bi-sun-fill';
                btn.title = 'Cambiar a modo claro';
            } else {
                html.removeAttribute('data-theme');
                icon.className = 'bi bi-moon-fill';
                btn.title = 'Cambiar a modo oscuro';
            }
        }

        // Aplicar estado guardado al cargar (el inline script ya puso el atributo,
        // aquí solo actualizamos el ícono)
        aplicarTema(localStorage.getItem('tema') === 'oscuro');

        btn.addEventListener('click', function () {
            const esOscuro = html.getAttribute('data-theme') === 'dark';
            const nuevo    = !esOscuro;
            localStorage.setItem('tema', nuevo ? 'oscuro' : 'claro');
            aplicarTema(nuevo);
        });
    })();
    </script>

    @yield('scripts')

    {{-- ── initListView: vista tabla/tarjetas + ordenamiento ── --}}
    <script>
    function initListView(pageKey, defaultBy, defaultDir) {
        var card = document.querySelector('.data-card:not([data-lv-init])');
        // Permite múltiples toolbars en la misma página (usuarios tiene dos)
        document.querySelectorAll('.data-card:not([data-lv-init])').forEach(function(dataCard, idx) {
            dataCard.setAttribute('data-lv-init', '1');
            var key = pageKey + (idx > 0 ? '_' + idx : '');

            var toolbar  = dataCard.querySelector('.list-toolbar');
            if (!toolbar) return;

            var btnVistas = toolbar.querySelectorAll('.btn-vista');
            var selSort   = toolbar.querySelector('.sort-select');
            var btnDir    = toolbar.querySelector('.btn-sortdir');
            var vTabla    = dataCard.querySelector('.view-tabla');
            var vTarjetas = dataCard.querySelector('.view-tarjetas');

            var view = localStorage.getItem('view_' + key) || 'tabla';
            var by   = localStorage.getItem('sortBy_' + key) || defaultBy;
            var dir  = localStorage.getItem('sortDir_' + key) || defaultDir;

            if (selSort) selSort.value = by;
            setView(view);
            setDir(dir);
            sort();

            // Toggle vista
            btnVistas.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    btnVistas.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    view = this.dataset.v;
                    localStorage.setItem('view_' + key, view);
                    setView(view);
                });
            });

            // Cambio de campo de ordenamiento
            if (selSort) selSort.addEventListener('change', function() {
                by = this.value;
                localStorage.setItem('sortBy_' + key, by);
                sort();
            });

            // Toggle dirección
            if (btnDir) btnDir.addEventListener('click', function() {
                dir = dir === 'asc' ? 'desc' : 'asc';
                localStorage.setItem('sortDir_' + key, dir);
                setDir(dir);
                sort();
            });

            function setView(v) {
                btnVistas.forEach(function(b) { b.classList.toggle('active', b.dataset.v === v); });
                if (vTabla)    vTabla.classList.toggle('oculto', v !== 'tabla');
                if (vTarjetas) {
                    vTarjetas.classList.toggle('activo', v === 'tarjetas');
                    if (v === 'tarjetas') vTarjetas.classList.add('grid-cards');
                }
            }

            function setDir(d) {
                if (btnDir) btnDir.innerHTML = d === 'asc'
                    ? '<i class="bi bi-sort-down"></i>'
                    : '<i class="bi bi-sort-up"></i>';
            }

            function sort() {
                // Ordenar filas de tabla
                if (vTabla) {
                    var tbody = vTabla.querySelector('tbody');
                    if (tbody) {
                        var rows = Array.from(tbody.querySelectorAll('tr.sort-row'));
                        rows.sort(function(a, b) {
                            var av = a.dataset[by] || '', bv = b.dataset[by] || '';
                            var cmp = isNaN(av) || av === '' ? av.localeCompare(bv, 'es') : parseFloat(av) - parseFloat(bv);
                            return dir === 'asc' ? cmp : -cmp;
                        });
                        rows.forEach(r => tbody.appendChild(r));
                    }
                }
                // Ordenar tarjetas
                if (vTarjetas) {
                    var cards = Array.from(vTarjetas.querySelectorAll('.sort-card'));
                    cards.sort(function(a, b) {
                        var av = a.dataset[by] || '', bv = b.dataset[by] || '';
                        var cmp = isNaN(av) || av === '' ? av.localeCompare(bv, 'es') : parseFloat(av) - parseFloat(bv);
                        return dir === 'asc' ? cmp : -cmp;
                    });
                    cards.forEach(c => vTarjetas.appendChild(c));
                }
            }
        });
    }
    </script>

</body>
</html>
