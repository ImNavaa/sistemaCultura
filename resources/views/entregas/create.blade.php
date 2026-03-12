@extends('layouts.app')
@section('title', 'Nueva Entrega')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-arrow-right"></i> Nueva Entrega</h2>
    <a href="{{ route('entregas.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('entregas.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Artículo <span class="text-danger">*</span></label>
                    <select name="articulo_id" id="articulo_id" class="form-select @error('articulo_id') is-invalid @enderror"
                            onchange="mostrarStock()">
                        <option value="">-- Seleccionar artículo --</option>
                        @foreach($articulos as $articulo)
                            <option value="{{ $articulo->id }}"
                                data-stock="{{ $articulo->cantidad_actual }}"
                                data-unidad="{{ $articulo->unidad }}"
                                {{ old('articulo_id', request('articulo')) == $articulo->id ? 'selected' : '' }}>
                                {{ $articulo->nombre }} — {{ $articulo->cantidad_actual }} {{ $articulo->unidad }}(s)
                            </option>
                        @endforeach
                    </select>
                    @error('articulo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="stockInfo" class="form-text text-success"></div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                    <input type="number" name="cantidad" step="0.01" min="0.01"
                           class="form-control @error('cantidad') is-invalid @enderror"
                           value="{{ old('cantidad') }}" placeholder="0">
                    @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha de entrega <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_entrega" class="form-control @error('fecha_entrega') is-invalid @enderror"
                           value="{{ old('fecha_entrega', date('Y-m-d')) }}">
                    @error('fecha_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Receptor <span class="text-danger">*</span></label>
                    <input type="text" name="receptor" class="form-control @error('receptor') is-invalid @enderror"
                           value="{{ old('receptor') }}" placeholder="Nombre de quien recibe">
                    @error('receptor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Responsable que entrega <span class="text-danger">*</span></label>
                    <select name="responsable_id" class="form-select @error('responsable_id') is-invalid @enderror">
                        <option value="">-- Seleccionar --</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('responsable_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsable_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"
                              placeholder="Notas adicionales">{{ old('observaciones') }}</textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-right"></i> Registrar Entrega
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function mostrarStock() {
    const select = document.getElementById('articulo_id');
    const option = select.options[select.selectedIndex];
    const info = document.getElementById('stockInfo');

    if (option.value) {
        info.textContent = `Disponible: ${option.dataset.stock} ${option.dataset.unidad}(s)`;
    } else {
        info.textContent = '';
    }
}

document.addEventListener('DOMContentLoaded', mostrarStock);
</script>
@endsection