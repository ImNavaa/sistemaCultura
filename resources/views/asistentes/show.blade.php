@extends('layouts.app')
@section('title', $asistente->nombreCompleto())
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon teal"><i class="bi bi-person-circle"></i></div>
        <div>
            <h2>{{ $asistente->nombreCompleto() }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('actividades.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asistentes.index') }}">Directorio</a></li>
                    <li class="breadcrumb-item active">{{ $asistente->nombre }}</li>
                </ol>
            </nav>
        </div>
    </div>
    @if(auth()->user()->puede('act_asistentes','editar'))
    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditarAsistente">
        <i class="bi bi-pencil me-1"></i>Editar datos
    </button>
    @endif
</div>

<div class="row g-3">
    {{-- Perfil --}}
    <div class="col-md-4">
        <div class="data-card">
            <div class="data-card-header"><div class="header-icon teal"><i class="bi bi-person"></i></div>Datos personales</div>
            <div class="p-3 text-center mb-2">
                <span style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#00695c,#00897b);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;">
                    {{ $asistente->iniciales() }}
                </span>
                <div class="fw-bold mt-2" style="font-size:1.05rem;">{{ $asistente->nombreCompleto() }}</div>
                @if($asistente->ocupacion)<div class="text-muted small">{{ $asistente->ocupacion }}</div>@endif
            </div>
            <div class="p-3 pt-0">
                <ul class="info-list">
                    @if($asistente->email)
                    <li><span class="info-key">Email</span><span class="info-val small">{{ $asistente->email }}</span></li>
                    @endif
                    @if($asistente->telefono)
                    <li><span class="info-key">Teléfono</span><span class="info-val">{{ $asistente->telefono }}</span></li>
                    @endif
                    @if($asistente->institucion)
                    <li><span class="info-key">Institución</span><span class="info-val">{{ $asistente->institucion }}</span></li>
                    @endif
                    @if($asistente->ciudad)
                    <li><span class="info-key">Ciudad</span><span class="info-val">{{ $asistente->ciudad }}</span></li>
                    @endif
                    @if($asistente->edad)
                    <li><span class="info-key">Edad</span><span class="info-val">{{ $asistente->edad }} años</span></li>
                    @endif
                    @if($asistente->genero)
                    <li><span class="info-key">Género</span><span class="info-val">{{ ucfirst(str_replace('_',' ',$asistente->genero)) }}</span></li>
                    @endif
                    @if($asistente->curp)
                    <li><span class="info-key">CURP</span><span class="info-val font-monospace small">{{ $asistente->curp }}</span></li>
                    @endif
                </ul>
                @if($asistente->notas)
                <div class="mt-3 small text-muted">{{ $asistente->notas }}</div>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-2 mt-1">
            <div class="col-6">
                <div class="stat-card"><div class="stat-card-icon teal"><i class="bi bi-calendar-check"></i></div>
                    <div><div class="stat-card-value">{{ $inscripciones->count() }}</div><div class="stat-card-label">Inscripciones</div></div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card"><div class="stat-card-icon green"><i class="bi bi-check2-circle"></i></div>
                    <div><div class="stat-card-value">{{ $inscripciones->filter(fn($i)=>$i->checkin)->count() }}</div><div class="stat-card-label">Asistencias</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial --}}
    <div class="col-md-8">
        <div class="data-card">
            <div class="data-card-header"><div class="header-icon teal"><i class="bi bi-clock-history"></i></div>Historial de actividades</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Folio</th><th>Actividad</th><th>Fecha</th><th class="text-center">Asistió</th></tr>
                    </thead>
                    <tbody>
                        @forelse($inscripciones as $insc)
                        <tr>
                            <td><span class="font-monospace small">{{ $insc->folio }}</span></td>
                            <td>
                                <a href="{{ route('actividades.show', $insc->actividad) }}" class="fw-semibold text-decoration-none" style="color:var(--text-main);">
                                    {{ $insc->actividad->nombre }}
                                </a>
                                <div class="small text-muted">{{ ucfirst($insc->actividad->tipo) }}</div>
                            </td>
                            <td class="small">{{ $insc->actividad->fecha_inicio->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($insc->checkin)
                                    <span class="badge" style="background:#dcfce7;color:#166534;font-size:.72rem;">
                                        <i class="bi bi-check2"></i> {{ $insc->checkin->hora_checkin->format('H:i') }}
                                    </span>
                                @else
                                    <span class="badge" style="background:#f1f5f9;color:#64748b;font-size:.72rem;">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><div class="empty-state"><i class="bi bi-calendar-x"></i><p>Sin inscripciones aún.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal editar --}}
@if(auth()->user()->puede('act_asistentes','editar'))
<div class="modal fade" id="modalEditarAsistente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--navy),var(--navy3));color:#fff;">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('asistentes.update', $asistente) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" value="{{ $asistente->nombre }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" value="{{ $asistente->apellidos }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $asistente->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $asistente->telefono }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Institución</label>
                            <input type="text" name="institucion" class="form-control" value="{{ $asistente->institucion }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" value="{{ $asistente->ciudad }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Edad</label>
                            <input type="number" name="edad" class="form-control" min="1" max="120" value="{{ $asistente->edad }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Género</label>
                            <select name="genero" class="form-select">
                                <option value="">No especificado</option>
                                <option value="femenino"         @selected($asistente->genero==='femenino')>Femenino</option>
                                <option value="masculino"        @selected($asistente->genero==='masculino')>Masculino</option>
                                <option value="otro"             @selected($asistente->genero==='otro')>Otro</option>
                                <option value="prefiero_no_decir" @selected($asistente->genero==='prefiero_no_decir')>Prefiero no decir</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CURP</label>
                            <input type="text" name="curp" class="form-control" value="{{ $asistente->curp }}" maxlength="18">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ocupación</label>
                            <input type="text" name="ocupacion" class="form-control" value="{{ $asistente->ocupacion }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2">{{ $asistente->notas }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-navy"><i class="bi bi-check-lg me-1"></i>Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
