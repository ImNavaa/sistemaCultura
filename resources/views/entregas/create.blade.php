@extends('layouts.app')
@section('title', 'Nueva Entrega')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon green"><i class="bi bi-box-arrow-right"></i></div>
        <div>
            <h2>Nueva Entrega</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('entregas.index') }}">Vales de Salida</a></li>
                    <li class="breadcrumb-item active">Nueva entrega</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('entregas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('error') }}
    </div>
@endif
@if($errors->has('items'))
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('items') }}
    </div>
@endif

<form action="{{ route('entregas.store') }}" method="POST" id="formEntrega">
@csrf
<div class="row g-3">

    {{-- Datos generales --}}
    <div class="col-12">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-info-circle me-2"></i>Datos generales del vale
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de entrega <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_entrega"
                               class="form-control @error('fecha_entrega') is-invalid @enderror"
                               value="{{ old('fecha_entrega', date('Y-m-d')) }}">
                        @error('fecha_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Receptor <span class="text-danger">*</span></label>
                        <input type="text" name="receptor"
                               class="form-control @error('receptor') is-invalid @enderror"
                               value="{{ old('receptor') }}" placeholder="Nombre de quien recibe">
                        @error('receptor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Unidad solicitante <span class="text-danger">*</span></label>
                        <select name="unidad_solicitante"
                                class="form-select @error('unidad_solicitante') is-invalid @enderror">
                            <option value="">-- Seleccionar --</option>
                            @foreach(['Teatro José González Echeverría','Ágora','Casa de Cultura Mateo Gallegos'] as $unidad)
                                <option value="{{ $unidad }}" {{ old('unidad_solicitante') === $unidad ? 'selected' : '' }}>
                                    {{ $unidad }}
                                </option>
                            @endforeach
                        </select>
                        @error('unidad_solicitante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Responsable que entrega</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="1"
                                  placeholder="Notas adicionales">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Artículos --}}
    <div class="col-12">
        <div class="data-card">
            <div class="data-card-header">
                <div class="header-icon green"><i class="bi bi-boxes"></i></div>
                Artículos a entregar
                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="btnAgregar">
                    <i class="bi bi-plus-circle me-1"></i> Agregar artículo
                </button>
            </div>
            <div class="table-responsive">
                <table class="table" id="tablaItems">
                    <thead>
                        <tr>
                            <th style="width:48%">Artículo</th>
                            <th style="width:18%">Cantidad</th>
                            <th style="width:28%">Disponible</th>
                            <th style="width:6%" class="text-center">—</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoItems"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
        <a href="{{ route('entregas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-navy px-4">
            <i class="bi bi-box-arrow-right me-1"></i> Registrar Vale de Salida
        </button>
    </div>

</div>
</form>

<template id="tmplFila">
    <tr class="fila-item">
        <td>
            <select name="items[__IDX__][articulo_id]" class="form-select form-select-sm sel-articulo" required>
                <option value="">-- Seleccionar artículo --</option>
                @foreach($articulos as $a)
                    <option value="{{ $a->id }}" data-stock="{{ $a->cantidad_actual }}" data-unidad="{{ $a->unidad }}">
                        {{ $a->nombre }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[__IDX__][cantidad]"
                   class="form-control form-control-sm inp-cantidad"
                   step="0.01" min="0.01" placeholder="0" required>
        </td>
        <td class="text-muted small lbl-stock align-middle" style="font-size:.8rem;">—</td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-action btn-outline-danger btn-eliminar" title="Quitar">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@section('scripts')
<script>
let idx = 0;

function agregarFila() {
    const tmpl = document.getElementById('tmplFila').innerHTML.replaceAll('__IDX__', idx++);
    const tmp = document.createElement('tbody');
    tmp.innerHTML = tmpl;
    const fila = tmp.firstElementChild;

    fila.querySelector('.sel-articulo').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const lbl = this.closest('tr').querySelector('.lbl-stock');
        lbl.textContent = opt.value
            ? `Disponible: ${opt.dataset.stock} ${opt.dataset.unidad}(s)`
            : '—';
    });

    fila.querySelector('.btn-eliminar').addEventListener('click', function () {
        const tbody = document.getElementById('cuerpoItems');
        if (tbody.querySelectorAll('.fila-item').length > 1) {
            this.closest('tr').remove();
        }
    });

    document.getElementById('cuerpoItems').appendChild(fila);
}

document.getElementById('btnAgregar').addEventListener('click', agregarFila);
agregarFila();

@if(old('items'))
    document.getElementById('cuerpoItems').innerHTML = '';
    const oldItems = @json(old('items'));
    oldItems.forEach((item, i) => {
        agregarFila();
        const fila = document.querySelectorAll('#cuerpoItems .fila-item')[i];
        const sel = fila.querySelector('.sel-articulo');
        const inp = fila.querySelector('.inp-cantidad');
        sel.value = item.articulo_id || '';
        inp.value = item.cantidad || '';
        sel.dispatchEvent(new Event('change'));
    });
@endif
</script>
@endsection
