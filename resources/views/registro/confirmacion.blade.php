@extends('layouts.public')
@section('title', 'Confirmación de registro')

@push('styles')
<style>
    .confirm-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 1.5rem;
    }
    .folio-box {
        background: #f0f4ff;
        border: 2px dashed #c5cae9;
        border-radius: 12px;
        padding: 1.25rem 2rem;
        text-align: center;
        margin: 1.5rem 0;
    }
    .folio-box .folio-label { font-size: .8rem; color: #64748b; text-transform: uppercase; letter-spacing: .06em; }
    .folio-box .folio-code  { font-size: 2rem; font-weight: 700; color: #1a237e; letter-spacing: .12em; }
</style>
@endpush

@section('content')
<div class="pub-card text-center" style="max-width:560px;margin:0 auto;">

    @if(session('success'))
        <div class="confirm-icon" style="background:#dcfce7;color:#16a34a;">
            <i class="bi bi-check-lg"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color:#16a34a;">¡Registro exitoso!</h3>
        <p class="text-muted">Hola <strong>{{ session('nombre') }}</strong>, quedaste inscrito(a) en:</p>
    @elseif(session('info'))
        <div class="confirm-icon" style="background:#dbeafe;color:#2563eb;">
            <i class="bi bi-info-lg"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color:#2563eb;">¡Ya estabas registrado!</h3>
        <p class="text-muted">Hola <strong>{{ session('nombre') }}</strong>, ya tenías un registro en:</p>
    @else
        <div class="confirm-icon" style="background:#f1f5f9;color:#64748b;">
            <i class="bi bi-calendar-check"></i>
        </div>
        <h3 class="fw-bold mb-1">Confirmación</h3>
    @endif

    <div class="py-2 px-3 rounded mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
        <div class="fw-bold fs-5">{{ $actividad->nombre }}</div>
        <div class="d-flex flex-wrap justify-content-center gap-3 text-muted small mt-2">
            @if($actividad->fecha_inicio)
                <span><i class="bi bi-calendar3 me-1"></i>
                    {{ \Carbon\Carbon::parse($actividad->fecha_inicio)->translatedFormat('d \d\e F Y') }}
                    @if($actividad->hora_inicio) a las {{ substr($actividad->hora_inicio, 0, 5) }} @endif
                </span>
            @endif
            @if($actividad->ubicacion)
                <span><i class="bi bi-geo-alt me-1"></i>{{ $actividad->ubicacion }}</span>
            @endif
        </div>
    </div>

    @if(session('folio'))
    <div class="folio-box">
        <div class="folio-label">Tu folio de registro</div>
        <div class="folio-code">{{ session('folio') }}</div>
        <div class="text-muted small mt-1">Guarda este número — te lo pedirán en la entrada</div>
    </div>
    @endif

    @if(session('correoEnviado'))
    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:.85rem;">
        <i class="bi bi-envelope-check me-1"></i>
        Te enviamos un correo de confirmación
        @if(filled($actividad->requisitos)) con los <strong>requisitos de la actividad</strong>@endif.
        Revisa tu bandeja de entrada.
    </div>
    @endif

    <p class="text-muted small mb-4">
        Si tienes alguna duda, comunícate con nosotros.
    </p>

    <div class="d-flex gap-2 justify-content-center">
        <a href="{{ route('registro.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Ver más actividades
        </a>
        <button onclick="window.print()" class="btn" style="background:#1a237e;color:#fff;">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
    </div>
</div>
@endsection
