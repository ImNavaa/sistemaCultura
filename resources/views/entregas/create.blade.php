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
@if($errors->has('items'))
    <div class="alert alert-danger">{{ $errors->first('items') }}</div>
@endif

<form action="{{ route('entregas.store') }}" method="POST" id="formEntrega">
@csrf
<div class="row g-3">

    {{-- ── Datos generales ─────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold"><i class="bi bi-info-circle me-1"></i>Datos generales</div>
            <div class="card-body">
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
                            @foreach([
                                'Teatro José González Echeverría',
                                'Ágora',
                                'Casa de Cultura Mateo Gallegos',
                            ] as $unidad)
                                <option value="{{ $unidad }}"
                                    {{ old('unidad_solicitante') === $unidad ? 'selected' : '' }}>
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

    {{-- ── Artículos ─────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-boxes me-1"></i>Artículos a entregar</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregar">
                    <i class="bi bi-plus-circle"></i> Agregar artículo
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="tablaItems">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:50%">Artículo</th>
                            <th style="width:20%">Cantidad</th>
                            <th style="width:25%">Disponible</th>
                            <th style="width:5%" class="text-center">—</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoItems">
                        {{-- Las filas se insertan por JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
        <a href="{{ route('entregas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-box-arrow-right"></i> Registrar Entrega
        </button>
    </div>

</div>
</form>

{{-- Template oculto para una fila --}}
<template id="tmplFila">
    <tr class="fila-item">
        <td>
            <select name="items[__IDX__][articulo_id]" class="form-select form-select-sm sel-articulo" required>
                <option value="">-- Seleccionar artículo --</option>
                @foreach($articulos as $a)
                    <option value="{{ $a->id }}"
                            data-stock="{{ $a->cantidad_actual }}"
                            data-unidad="{{ $a->unidad }}">
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
        <td class="text-muted small lbl-stock align-middle">—</td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar">
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
    const cuerpo = document.getElementById('cuerpoItems');
    const tr = document.createElement('tbody');
    tr.innerHTML = tmpl;
    const fila = tr.firstElementChild;

    // Actualizar stock al elegir artículo
    fila.querySelector('.sel-articulo').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const lbl = this.closest('tr').querySelector('.lbl-stock');
        lbl.textContent = opt.value
            ? `Disponible: ${opt.dataset.stock} ${opt.dataset.unidad}(s)`
            : '—';
    });

    // Eliminar fila
    fila.querySelector('.btn-eliminar').addEventListener('click', function () {
        const tbody = document.getElementById('cuerpoItems');
        if (tbody.querySelectorAll('.fila-item').length > 1) {
            this.closest('tr').remove();
        }
    });

    cuerpo.appendChild(fila);
}

document.getElementById('btnAgregar').addEventListener('click', agregarFila);

// Iniciar con una fila vacía
agregarFila();

// Restaurar filas si hay errores de validación (old input)
@if(old('items'))
    // Quitar la fila vacía inicial
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
