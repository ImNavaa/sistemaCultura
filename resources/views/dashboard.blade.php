@extends('layouts.app')
@section('title', 'Inicio — Sistema Cultura')

@push('styles')
<style>
/* ── Hero de bienvenida ── */
.dashboard-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: white;
}
.avatar-circle {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; font-weight: 700; flex-shrink: 0;
}
.badge-rol {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: .75rem;
    color: white;
}
#reloj { font-size: 1.2rem; font-variant-numeric: tabular-nums; color: white; }

/* ── Cards de módulos ── */
.module-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
    transition: box-shadow .2s, transform .2s;
    cursor: pointer;
}
.module-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.module-card-icon {
    width: 48px; height: 48px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.module-card-body { flex: 1; }
.module-card-title { font-weight: 600; font-size: .95rem; color: #212529; margin-bottom: .25rem; }
.module-card-desc  { font-size: .8rem; color: #6c757d; line-height: 1.4; }
.module-card-arrow { font-size: 1.2rem; align-self: flex-end; }

/* ── Quick cards ── */
.quick-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: .85rem 1.1rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    font-size: .9rem;
    color: #343a40;
    transition: background .15s, box-shadow .15s;
}
.quick-card:hover { background: #f8f9fa; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
.quick-card i { font-size: 1.25rem; }
</style>
@endpush

@section('content')

{{-- Header de bienvenida --}}
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="avatar-circle">
            {{ strtoupper(substr($usuario->name, 0, 1)) }}
        </div>
        <div>
            <h4 class="mb-0 fw-bold">Bienvenido, {{ explode(' ', $usuario->name)[0] }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                @if($usuario->rol)
                    <span class="badge-rol">
                        <i class="bi bi-shield-check"></i>
                        {{ ucfirst(str_replace('_', ' ', $usuario->rol->nombre)) }}
                    </span>
                @endif
                @if($usuario->cargo)
                    <span style="color:rgba(255,255,255,.65); font-size:.85rem;">— {{ $usuario->cargo }}</span>
                @endif
            </div>
        </div>
        <div class="ms-auto text-end d-none d-md-block">
            <div style="color:rgba(255,255,255,.6); font-size:.8rem;">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </div>
            <div id="reloj" class="fw-semibold">--:--:--</div>
        </div>
    </div>
</div>

{{-- Módulos accesibles --}}
@if(count($modulosPermitidos) > 0)
<h6 class="text-muted text-uppercase mb-3" style="letter-spacing:.08em; font-size:.75rem;">
    <i class="bi bi-grid-3x3-gap"></i> Mis módulos
</h6>
<div class="row g-3 mb-4">
    @foreach($modulosPermitidos as $modulo)
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <a href="{{ route($modulo['ruta']) }}" class="text-decoration-none">
            <div class="module-card h-100">
                <div class="module-card-icon bg-{{ $modulo['color'] }} bg-opacity-10 text-{{ $modulo['color'] }}">
                    <i class="bi {{ $modulo['icono'] }}"></i>
                </div>
                <div class="module-card-body">
                    <div class="module-card-title">{{ $modulo['nombre'] }}</div>
                    <div class="module-card-desc">{{ $modulo['descripcion'] }}</div>
                </div>
                <div class="module-card-arrow text-{{ $modulo['color'] }}">
                    <i class="bi bi-arrow-right-circle"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    No tienes módulos asignados. Contacta al administrador para configurar tus permisos.
</div>
@endif

{{-- Accesos rápidos --}}
<h6 class="text-muted text-uppercase mb-3" style="letter-spacing:.08em; font-size:.75rem;">
    <i class="bi bi-lightning"></i> Accesos rápidos
</h6>
<div class="row g-3">
    @if($usuario->puede('usuarios', 'editar'))
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('usuarios.index') }}" class="text-decoration-none">
            <div class="quick-card">
                <i class="bi bi-shield-lock text-primary"></i>
                <span>Gestionar permisos de empleados</span>
            </div>
        </a>
    </div>
    @endif
    @if($usuario->puede('usuarios', 'crear'))
    <div class="col-12 col-sm-6 col-md-4">
        <a href="{{ route('usuarios.create', ['tipo' => 'con_acceso']) }}" class="text-decoration-none">
            <div class="quick-card">
                <i class="bi bi-person-plus text-secondary"></i>
                <span>Registrar nuevo empleado</span>
            </div>
        </a>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
function actualizarReloj() {
    const ahora = new Date();
    const hora  = ahora.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el = document.getElementById('reloj');
    if (el) el.textContent = hora;
}
actualizarReloj();
setInterval(actualizarReloj, 1000);
</script>
@endsection
