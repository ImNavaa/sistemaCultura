@extends('layouts.app')
@section('title', 'Nuevo Artículo')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon green"><i class="bi bi-plus-circle"></i></div>
        <div>
            <h2>Nuevo Artículo</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('almacen.index') }}">Almacén</a></li>
                    <li class="breadcrumb-item active">Nuevo artículo</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('almacen.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="form-card">
    <div class="form-card-header">
        <i class="bi bi-box-seam me-2"></i> Información del artículo
    </div>
    <div class="form-card-body">
        <form action="{{ route('almacen.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del artículo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej: Papel higiénico">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                    <select name="unidad" class="form-select @error('unidad') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        @foreach(['pieza'=>'Pieza','caja'=>'Caja','litro'=>'Litro','metro'=>'Metro','rollo'=>'Rollo','par'=>'Par','juego'=>'Juego','kg'=>'Kilogramo','paquete'=>'Paquete'] as $val => $label)
                            <option value="{{ $val }}" {{ old('unidad') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Detalles adicionales del artículo">{{ old('descripcion') }}</textarea>
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
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-navy px-4">
                        <i class="bi bi-save me-1"></i> Guardar Artículo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
