@extends('layouts.app')
@section('title', 'Nuevo Artículo')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Nuevo Artículo</h2>
    <a href="{{ route('almacen.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('almacen.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del artículo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej: Papel para baño">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Unidad <span class="text-danger">*</span></label>
                    <select name="unidad" class="form-select @error('unidad') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        <option value="pieza" {{ old('unidad') == 'pieza' ? 'selected' : '' }}>Pieza</option>
                        <option value="caja" {{ old('unidad') == 'caja' ? 'selected' : '' }}>Caja</option>
                        <option value="litro" {{ old('unidad') == 'litro' ? 'selected' : '' }}>Litro</option>
                        <option value="metro" {{ old('unidad') == 'metro' ? 'selected' : '' }}>Metro</option>
                        <option value="rollo" {{ old('unidad') == 'rollo' ? 'selected' : '' }}>Rollo</option>
                        <option value="par" {{ old('unidad') == 'par' ? 'selected' : '' }}>Par</option>
                        <option value="juego" {{ old('unidad') == 'juego' ? 'selected' : '' }}>Juego</option>
                        <option value="kg" {{ old('unidad') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                    </select>
                    @error('unidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Detalles del artículo">{{ old('descripcion') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Cantidad inicial <span class="text-danger">*</span></label>
                    <input type="number" name="cantidad_actual" step="0.01" min="0"
                           class="form-control @error('cantidad_actual') is-invalid @enderror"
                           value="{{ old('cantidad_actual', 0) }}">
                    @error('cantidad_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Responsable</label>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Artículo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection