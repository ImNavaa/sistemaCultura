@extends('layouts.app')
@section('title', 'Nueva Actividad')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon blue"><i class="bi bi-person-badge"></i></div>
        <div>
            <h2>Nueva Actividad</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('actividades.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header"><i class="bi bi-calendar-plus me-2"></i>Datos de la actividad</div>
    <div class="form-card-body">
        @if($errors->any())
        <div class="alert alert-danger mb-3">
            @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('actividades.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <div class="form-section-title">Información general</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select" required>
                        @foreach(App\Models\Actividad::tipos() as $t)
                        <option value="{{ $t }}" @selected(old('tipo') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Instructor / Ponente</label>
                    <input type="text" name="instructor" class="form-control" value="{{ old('instructor') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">
                        <i class="bi bi-clipboard-check me-1 text-warning"></i>Requisitos para participar
                        <span class="text-muted fw-normal" style="font-size:.8rem;">(opcional — se enviará por correo al registrarse)</span>
                    </label>
                    <textarea name="requisitos" class="form-control" rows="4"
                              placeholder="Ej:&#10;- Traer identificación oficial&#10;- Laptop con software X instalado&#10;- Conocimientos básicos de...">{{ old('requisitos') }}</textarea>
                </div>

                <div class="col-12 mt-2">
                    <div class="form-section-title">Fecha y hora</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hora fin</label>
                    <input type="time" name="hora_fin" class="form-control" value="{{ old('hora_fin') }}">
                </div>

                <div class="col-12 mt-2">
                    <div class="form-section-title">Configuración</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Modalidad <span class="text-danger">*</span></label>
                    <select name="modalidad" class="form-select" required>
                        <option value="presencial" @selected(old('modalidad','presencial')==='presencial')>Presencial</option>
                        <option value="virtual"    @selected(old('modalidad')==='virtual')>Virtual</option>
                        <option value="hibrido"    @selected(old('modalidad')==='hibrido')>Híbrido</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cupo máximo <span class="text-muted small">(vacío = sin límite)</span></label>
                    <input type="number" name="cupo_maximo" class="form-control" min="1" value="{{ old('cupo_maximo') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado <span class="text-danger">*</span></label>
                    <select name="estado" class="form-select" required>
                        @foreach(App\Models\Actividad::estados() as $e)
                        <option value="{{ $e }}" @selected(old('estado','borrador')===$e)>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>

                @include('actividades._form-builder')

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-navy"><i class="bi bi-check-lg me-1"></i>Guardar actividad</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
