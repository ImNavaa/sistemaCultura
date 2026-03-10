@extends('layouts.app')

@section('title', 'Nuevo Oficio')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Nuevo Oficio</h2>
    <a href="{{ route('oficios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('oficios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                        value="{{ old('fecha') }}">
                    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hora Inicio</label>
                    <input type="time" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror"
                        value="{{ old('hora_inicio', $oficio->hora_inicio ?? '') }}">
                    @error('hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Fin</label>
                    <input type="time" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror"
                        value="{{ old('hora_fin', $oficio->hora_fin ?? '') }}">
                    @error('hora_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Número de Oficio <span class="text-danger">*</span></label>
                    <input type="text" name="numero_oficio" class="form-control @error('numero_oficio') is-invalid @enderror"
                        value="{{ old('numero_oficio') }}" placeholder="Ej: OF-2024-001">
                    @error('numero_oficio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nombre del Evento <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_evento" class="form-control @error('nombre_evento') is-invalid @enderror"
                        value="{{ old('nombre_evento') }}" placeholder="Nombre del evento">
                    @error('nombre_evento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Organizador <span class="text-danger">*</span></label>
                    <input type="text" name="organizador" class="form-control @error('organizador') is-invalid @enderror"
                        value="{{ old('organizador') }}" placeholder="Nombre del organizador">
                    @error('organizador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">¿Se cobró? <span class="text-danger">*</span></label>
                    <select name="cobrado" id="cobrado" class="form-select @error('cobrado') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        <option value="1" {{ old('cobrado') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('cobrado') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('cobrado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3" id="monto_field">
                    <label class="form-label">Monto Cobrado</label>
                    <input type="number" name="monto_cobrado" step="0.01" min="0"
                        class="form-control @error('monto_cobrado') is-invalid @enderror"
                        value="{{ old('monto_cobrado') }}" placeholder="0.00">
                    @error('monto_cobrado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Foto / Archivo del documento</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                        accept="image/*,.pdf">
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Oficio
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const cobradoSelect = document.getElementById('cobrado');
    const montoField = document.getElementById('monto_field');

    function toggleMonto() {
        montoField.style.display = cobradoSelect.value == '1' ? 'block' : 'none';
    }

    cobradoSelect.addEventListener('change', toggleMonto);
    toggleMonto();
</script>
@endsection