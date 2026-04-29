@extends('layouts.app')
@section('title', 'Editar — ' . $almacen->nombre)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon amber"><i class="bi bi-pencil"></i></div>
        <div>
            <h2>Editar Artículo</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('almacen.index') }}">Almacén</a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
        <i class="bi bi-box-seam me-2"></i> {{ $almacen->nombre }}
    </div>
    <div class="form-card-body">
        <form action="{{ route('almacen.update', $almacen) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label">Nombre del artículo <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $almacen->nombre) }}">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Unidad de medida <span class="text-danger">*</span></label>
                    <select name="unidad" class="form-select @error('unidad') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        @foreach(['pieza'=>'Pieza','caja'=>'Caja','litro'=>'Litro','metro'=>'Metro','rollo'=>'Rollo','par'=>'Par','juego'=>'Juego','kg'=>'Kilogramo','paquete'=>'Paquete'] as $val => $label)
                            <option value="{{ $val }}" {{ old('unidad', $almacen->unidad) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $almacen->descripcion) }}</textarea>
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
                        <option value="">-- Seleccionar --</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                {{ old('responsable_id', $almacen->responsable_id) == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-navy px-4">
                        <i class="bi bi-save me-1"></i> Actualizar Artículo
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
