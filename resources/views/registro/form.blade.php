@extends('layouts.public')
@section('title', 'Registro — ' . $actividad->nombre)

@php
$config = $actividad->configFormulario();
$campos = $config['campos'];
$preguntas = $config['preguntas_extra'];

$estaOculto   = fn($c) => ($campos[$c] ?? 'opcional') === 'oculto';
$esRequerido  = fn($c) => ($campos[$c] ?? 'opcional') === 'requerido';
$starIf       = fn($c) => $esRequerido($c) ? '<span class="required-star">*</span>' : '';

$hayDatosPers = ! ($estaOculto('email') && $estaOculto('telefono') && $estaOculto('edad') && $estaOculto('genero'));
$hayDatosProf = ! ($estaOculto('institucion') && $estaOculto('ocupacion') && $estaOculto('ciudad') && $estaOculto('curp'));
@endphp

@push('styles')
<style>
    .form-label { font-weight: 600; font-size: .87rem; }
    .required-star { color: #dc2626; }
    .section-title {
        font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #64748b; margin-bottom: .75rem;
        padding-bottom: .5rem; border-bottom: 1px solid #e2e8f0;
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
                        @if($actividad->hora_inicio) a las {{ substr($actividad->hora_inicio, 0, 5) }} @endif
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

        {{-- Nombre y apellidos: siempre --}}
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

            @unless($estaOculto('email'))
            <div class="col-sm-6">
                <label class="form-label">Correo electrónico {!! $starIf('email') !!}</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="correo@ejemplo.com"
                       {{ $esRequerido('email') ? 'required' : '' }}>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if(! $esRequerido('email'))
                    <div class="form-text">Si ya te registraste antes, se reconocerá tu perfil.</div>
                @endif
            </div>
            @endunless

            @unless($estaOculto('telefono'))
            <div class="col-sm-6">
                <label class="form-label">Teléfono {!! $starIf('telefono') !!}</label>
                <input type="tel" name="telefono"
                       class="form-control @error('telefono') is-invalid @enderror"
                       value="{{ old('telefono') }}" placeholder="(000) 000-0000"
                       {{ $esRequerido('telefono') ? 'required' : '' }}>
                @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless

            @unless($estaOculto('edad'))
            <div class="col-sm-4">
                <label class="form-label">Edad {!! $starIf('edad') !!}</label>
                <input type="number" name="edad"
                       class="form-control @error('edad') is-invalid @enderror"
                       value="{{ old('edad') }}" min="1" max="120" placeholder="Ej. 30"
                       {{ $esRequerido('edad') ? 'required' : '' }}>
                @error('edad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless

            @unless($estaOculto('genero'))
            <div class="col-sm-{{ $estaOculto('edad') ? '12' : '8' }}">
                <label class="form-label">Género {!! $starIf('genero') !!}</label>
                <select name="genero" class="form-select @error('genero') is-invalid @enderror"
                        {{ $esRequerido('genero') ? 'required' : '' }}>
                    <option value="">— Seleccionar —</option>
                    @foreach(['femenino' => 'Femenino', 'masculino' => 'Masculino', 'otro' => 'Otro', 'prefiero_no_decir' => 'Prefiero no decir'] as $val => $label)
                        <option value="{{ $val }}" {{ old('genero') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('genero')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless
        </div>

        {{-- Datos profesionales --}}
        @if($hayDatosProf)
        <div class="section-title"><i class="bi bi-briefcase me-1"></i>Datos profesionales / académicos</div>
        <div class="row g-3 mb-4">
            @unless($estaOculto('institucion'))
            <div class="col-sm-6">
                <label class="form-label">Institución / Empresa {!! $starIf('institucion') !!}</label>
                <input type="text" name="institucion"
                       class="form-control @error('institucion') is-invalid @enderror"
                       value="{{ old('institucion') }}" placeholder="Ej. Universidad de Sonora"
                       {{ $esRequerido('institucion') ? 'required' : '' }}>
                @error('institucion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless

            @unless($estaOculto('ocupacion'))
            <div class="col-sm-6">
                <label class="form-label">Ocupación {!! $starIf('ocupacion') !!}</label>
                <input type="text" name="ocupacion"
                       class="form-control @error('ocupacion') is-invalid @enderror"
                       value="{{ old('ocupacion') }}" placeholder="Ej. Estudiante, Docente..."
                       {{ $esRequerido('ocupacion') ? 'required' : '' }}>
                @error('ocupacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless

            @unless($estaOculto('ciudad'))
            <div class="col-sm-6">
                <label class="form-label">Ciudad {!! $starIf('ciudad') !!}</label>
                <input type="text" name="ciudad"
                       class="form-control @error('ciudad') is-invalid @enderror"
                       value="{{ old('ciudad') }}" placeholder="Ej. Hermosillo"
                       {{ $esRequerido('ciudad') ? 'required' : '' }}>
                @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless

            @unless($estaOculto('curp'))
            <div class="col-sm-6">
                <label class="form-label">CURP {!! $starIf('curp') !!}</label>
                <input type="text" name="curp"
                       class="form-control @error('curp') is-invalid @enderror"
                       value="{{ old('curp') }}" placeholder="18 caracteres" maxlength="18"
                       style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()"
                       {{ $esRequerido('curp') ? 'required' : '' }}>
                @error('curp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endunless
        </div>
        @endif

        {{-- Preguntas extra --}}
        @if(count($preguntas) > 0)
        <div class="section-title"><i class="bi bi-chat-square-text me-1"></i>Información adicional</div>
        <div class="row g-3 mb-4">
            @foreach($preguntas as $i => $pregunta)
            <div class="col-12">
                <label class="form-label">
                    {{ $pregunta['label'] }}
                    @if($pregunta['requerido']) <span class="required-star">*</span> @endif
                </label>

                @if($pregunta['tipo'] === 'seleccion' && count($pregunta['opciones'] ?? []) > 0)
                    <select name="extra_{{ $i }}"
                            class="form-select @error("extra_{$i}") is-invalid @enderror"
                            {{ $pregunta['requerido'] ? 'required' : '' }}>
                        <option value="">— Seleccionar —</option>
                        @foreach($pregunta['opciones'] as $opcion)
                            <option value="{{ $opcion }}" {{ old("extra_{$i}") === $opcion ? 'selected' : '' }}>
                                {{ $opcion }}
                            </option>
                        @endforeach
                    </select>
                @elseif($pregunta['tipo'] === 'texto_largo')
                    <textarea name="extra_{{ $i }}" rows="3"
                              class="form-control @error("extra_{$i}") is-invalid @enderror"
                              placeholder="Escribe tu respuesta..."
                              {{ $pregunta['requerido'] ? 'required' : '' }}>{{ old("extra_{$i}") }}</textarea>
                @else
                    <input type="text" name="extra_{{ $i }}"
                           class="form-control @error("extra_{$i}") is-invalid @enderror"
                           value="{{ old("extra_{$i}") }}"
                           placeholder="Escribe tu respuesta..."
                           {{ $pregunta['requerido'] ? 'required' : '' }}>
                @endif
                @error("extra_{$i}")<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @endforeach
        </div>
        @endif

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
