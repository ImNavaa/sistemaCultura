@extends('layouts.app')

@section('title', 'Nuevo Registro de Tiempo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock"></i> Nuevo Registro de Tiempo</h2>
    <a href="{{ route('tiempo.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('tiempo.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Empleado <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">-- Seleccionar empleado --</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}"
                                {{ old('user_id', request('empleado')) == $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->name }} — {{ $empleado->cargo ?? 'Sin cargo' }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', date('Y-m-d')) }}">
                    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Categoría <span class="text-danger">*</span></label>
                    <select name="categoria" id="categoria" class="form-select @error('categoria') is-invalid @enderror"
                            onchange="actualizarTipos()">
                        <option value="">-- Seleccionar --</option>
                        <option value="favor" {{ old('categoria') === 'favor' ? 'selected' : '' }}>✅ A favor (generó tiempo)</option>
                        <option value="compensacion" {{ old('categoria') === 'compensacion' ? 'selected' : '' }}>🔴 Compensación (usó tiempo)</option>
                    </select>
                    @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                        <option value="">-- Primero selecciona categoría --</option>
                    </select>
                    @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Horas <span class="text-danger">*</span></label>
                    <input type="number" name="horas" step="0.25" min="0.25" max="24"
                           class="form-control @error('horas') is-invalid @enderror"
                           value="{{ old('horas') }}" placeholder="Ej: 1.5 = 1h 30min">
                    @error('horas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control"
                           value="{{ old('descripcion') }}" placeholder="Detalles adicionales">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Registro
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const tiposPorCategoria = {
    favor: [
        { value: 'horas_extra',     label: 'Horas extra' },
        { value: 'evento_especial', label: 'Evento especial' },
        { value: 'dia_descanso',    label: 'Trabajó día de descanso' },
        { value: 'apoyo_adicional', label: 'Apoyo o cubrimiento de turno' },
    ],
    compensacion: [
        { value: 'salida_temprana', label: 'Salida temprana' },
        { value: 'horas_libres',    label: 'Horas libres' },
        { value: 'dia_libre',       label: 'Día libre completo' },
    ]
};

function actualizarTipos() {
    const categoria = document.getElementById('categoria').value;
    const tipoSelect = document.getElementById('tipo');
    tipoSelect.innerHTML = '<option value="">-- Seleccionar tipo --</option>';

    if (tiposPorCategoria[categoria]) {
        tiposPorCategoria[categoria].forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.value;
            opt.textContent = t.label;
            tipoSelect.appendChild(opt);
        });
    }
}

// Si hay old value, restaurar
document.addEventListener('DOMContentLoaded', function() {
    const oldCategoria = '{{ old("categoria") }}';
    const oldTipo = '{{ old("tipo") }}';
    if (oldCategoria) {
        document.getElementById('categoria').value = oldCategoria;
        actualizarTipos();
        if (oldTipo) document.getElementById('tipo').value = oldTipo;
    }
});
</script>
@endsection