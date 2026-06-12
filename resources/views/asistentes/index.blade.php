@extends('layouts.app')
@section('title', 'Directorio de Asistentes')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-people"></i></div>
        <div>
            <h2>Directorio de Asistentes</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('actividades.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active">Directorio</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form method="GET" class="mb-3 d-flex gap-2 align-items-center">
    <div class="input-group" style="max-width:360px;">
        <span class="input-group-text" style="background:var(--bg-card);border-color:var(--border-color);"><i class="bi bi-search text-muted"></i></span>
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, email o institución…"
               value="{{ $q }}" style="background:var(--bg-card);border-color:var(--border-color);color:var(--text-main);">
    </div>
    <button type="submit" class="btn btn-navy btn-sm"><i class="bi bi-funnel me-1"></i>Buscar</button>
    @if($q)<a href="{{ route('asistentes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x me-1"></i>Limpiar</a>@endif
</form>

<div class="data-card">
    <div class="data-card-header">
        <div class="header-icon teal"><i class="bi bi-people"></i></div>
        Personas registradas
        <span class="badge ms-2 bg-secondary">{{ $asistentes->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Contacto</th>
                    <th>Institución</th>
                    <th>Ciudad</th>
                    <th class="text-center">Eventos</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asistentes as $ast)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#00695c,#00897b);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;flex-shrink:0;">{{ $ast->iniciales() }}</span>
                            <div>
                                <a href="{{ route('asistentes.show', $ast) }}" class="fw-semibold text-decoration-none" style="color:var(--text-main);">
                                    {{ $ast->nombreCompleto() }}
                                </a>
                                @if($ast->ocupacion)<div class="small text-muted">{{ $ast->ocupacion }}</div>@endif
                            </div>
                        </div>
                    </td>
                    <td class="small">
                        @if($ast->email)<div>{{ $ast->email }}</div>@endif
                        @if($ast->telefono)<div class="text-muted">{{ $ast->telefono }}</div>@endif
                        @if(! $ast->email && ! $ast->telefono)—@endif
                    </td>
                    <td class="small text-muted">{{ $ast->institucion ?? '—' }}</td>
                    <td class="small text-muted">{{ $ast->ciudad ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background:#e3f2fd;color:#1565c0;">{{ $ast->inscripciones_count }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('asistentes.show', $ast) }}" class="btn btn-action btn-outline-primary" title="Ver historial"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-people"></i><p>No se encontraron personas.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($asistentes->hasPages())
    <div class="p-3 d-flex justify-content-center">{{ $asistentes->links() }}</div>
    @endif
</div>

@endsection
