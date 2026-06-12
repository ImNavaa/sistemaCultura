@extends('layouts.public')
@section('title', 'Registro — ' . $actividad->nombre)

@push('styles')
<style>
    .form-label { font-weight: 600; font-size: .87rem; }
    .required-star { color: #dc2626; }
    .section-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: .75rem;
        padding-bottom: .5rem;
        border-bottom: 1px solid #e2e8f0;
    }
</style>
@endpush

@section('content')

{{-- Encabezado actividad --}}
<div class="pub-card mb-4" style="border-left:5px solid #1a237e;">
    <div class="d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;border-radius:12px;background:#e8eaf6;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
            <i class="bi bi-calendar-event" style="color:#1a237e;"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">{{ $actividad->nombre }}</h4>
            <div class="d-flex flex-wrap gap-3 text-muted small mt-1">
                @if($actividad->fecha_inicio)
                    <span><i class="bi bi-calendar3 me-1"></i>
                        {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->translatedFormat('d \d\e F Y') }}
                        @if($actividad->hora_inicio)
                            a las {{ substr($actividad->hora_inicio, 0, 5) }}
                        @endif
                    </span>
                @endif
                @if($actividad->ubicacion)
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $actividad->ubicacion }}</span>
                @endif
                @if($actividad->instructor)
                    <span><i class="bi bi-person me-1"></i>{{ $actividad->instructor }}</span>
                @endif
                @if($actividad->cupo_maximo)
                    @php $inscritos = $actividad->inscripcionesActivas()->count(); @endphp
                    <span><i class="bi bi-people me-1"></i>{{ $actividad->cupo_maximo - $inscritos }} lugar(es) disponibles</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Formulario --}}
<div class="pub-card">
    <h5 class="fw-bold mb-1">Llena tus datos para inscribirte</h5>
    <p class="text-muted small mb-4">Los campos marcados con <span class="required-star">*</span> son obligatorios.</p>

    <form method="POST" action="{{ route('registro.store', $actividad) }}" novalidate>
        @csrf

        {{-- Datos personales --}}
        <div class="section-title"><i class="bi bi-person me-1"></i>Datos personales</div>
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label class="form-label">Nombre(s) <span class="required-star">*</span></label>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre') }}" placeholder="Ej. María" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
                <label class="form-label">Apellidos <span class="required-star">*</span></label>
                <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror"
                       value="{{ old('apellidos') }}" placeholder="Ej. García López" required>
                @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Si ya te registraste antes, se reconocerá tu perfil.</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-control"
                       value="{{ old('telefono') }}" placeholder="(000) 000-0000">
            </div>
            <div class="col-sm-4">
                <label class="form-label">Edad</label>
                <input type="number" name="edad" class="form-control @error('edad') is-invalid @enderror"
                       value="{{ old('edad') }}" min="1" max="120" placeholder="Ej. 30">
                @error('edad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-8">
                <label class="form-label">Género</label>
                <select name="genero" class="form-select">
                    <option value="">— Prefiero no indicar —</option>
                    @foreach(['femenino' => 'Femenino', 'masculino' => 'Masculino', 'otro' => 'Otro', 'prefiero_no_decir' => 'Prefiero no decir'] as $val => $label)
                        <option value="{{ $val }}" {{ old('genero') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Datos profesionales --}}
        <div class="section-title"><i class="bi bi-briefcase me-1"></i>Datos profesionales / académicos</div>
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label class="form-label">Institución / Empresa</label>
                <input type="text" name="institucion" class="form-control"
                       value="{{ old('institucion') }}" placeholder="Ej. Universidad de Sonora">
            </div>
            <div class="col-sm-6">
                <label class="form-label">Ocupación</label>
                <input type="text" name="ocupacion" class="form-control"
                       value="{{ old('ocupacion') }}" placeholder="Ej. Estudiante, Docente, etc.">
            </div>
            <div class="col-sm-6">
                <label class="form-label">Ciudad</label>
                <input type="text" name="ciudad" class="form-control"
                       value="{{ old('ciudad') }}" placeholder="Ej. Hermosillo">
            </div>
            <div class="col-sm-6">
                <label class="form-label">CURP <span class="text-muted small">(opcional)</span></label>
                <input type="text" name="curp" class="form-control"
                       value="{{ old('curp') }}" placeholder="18 caracteres" maxlength="18"
                       style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-lg px-5" style="background:#1a237e;color:#fff;">
                <i class="bi bi-check-circle me-2"></i>Inscribirme
            </button>
            <a href="{{ route('registro.index') }}" class="btn btn-lg btn-outline-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection
