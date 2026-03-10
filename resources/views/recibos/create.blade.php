@extends('layouts.app')

@section('title', 'Nuevo Recibo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Nuevo Recibo</h2>
    <a href="{{ route('recibos.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('recibos.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                        value="{{ old('fecha') }}">
                    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Importe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="importe" step="0.01" min="0"
                            class="form-control @error('importe') is-invalid @enderror"
                            value="{{ old('importe') }}" placeholder="0.00">
                    </div>
                    @error('importe') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

                <div class="col-md-12">
                    <label class="form-label">Concepto <span class="text-danger">*</span></label>
                    <textarea name="concepto" rows="3" class="form-control @error('concepto') is-invalid @enderror"
                        placeholder="Descripción del concepto">{{ old('concepto') }}</textarea>
                    @error('concepto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Foto / Archivo del documento</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                        accept="image/*,.pdf">
                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Recibo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection