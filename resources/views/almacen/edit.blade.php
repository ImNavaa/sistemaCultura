@extends('layouts.app')
@section('title', 'Editar Artículo')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Editar Artículo</h2>
    <a href="{{ route('almacen.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('almacen.update', $almacen) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label">Nombre del artículo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $almacen->nombre) }}">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Unidad <span class="text-danger">*</span></label>
                    <select name="unidad" class="form-select @error('unidad') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        @foreach(['pieza','caja','litro','metro','rollo','par','juego','kg','paquete'] as $unidad)
                            <option value="{{ $unidad }}" {{ old('unidad', $almacen->unidad) == $unidad ? 'selected' : '' }}>
                                {{ ucfirst($unidad) }}
                            </option>
                        @endforeach
                    </select>
                    @error('unidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"
                              placeholder="Detalles del artículo">{{ old('descripcion', $almacen->descripcion) }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Cantidad actual <span class="text-danger">*</span></label>
                    <input type="number" name="cantidad_actual" step="0.01" min="0"
                           class="form-control @error('cantidad_actual') is-invalid @enderror"
                           value="{{ old('cantidad_actual', $almacen->cantidad_actual) }}">
                    @error('cantidad_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Responsable <span class="text-danger">*</span></label>
                    <select name="responsable_id" class="form-select @error('responsable_id') is-invalid @enderror">
                        <option value="">-- Seleccionar responsable --</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                {{ old('responsable_id', $almacen->responsable_id) == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Artículo
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection