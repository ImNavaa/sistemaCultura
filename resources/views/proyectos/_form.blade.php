@php
    $colorActual = old('color', $proyecto->color ?? '#3a7bd5');
@endphp

<div class="row g-3">
    {{-- Título --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
        <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
               value="{{ old('titulo', $proyecto->titulo ?? '') }}" required>
        @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Descripción --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $proyecto->descripcion ?? '') }}</textarea>
    </div>

    {{-- Estado + Color --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
        <select name="estado" class="form-select">
            @foreach(['activo' => 'Activo', 'pausado' => 'Pausado', 'completado' => 'Completado', 'cancelado' => 'Cancelado'] as $val => $label)
            <option value="{{ $val }}" {{ old('estado', $proyecto->estado ?? 'activo') === $val ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Color del proyecto</label>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-1">
            @foreach($colores as $hex)
            <label class="m-0" style="cursor:pointer;" title="{{ $hex }}">
                <input type="radio" name="color" value="{{ $hex }}"
                       class="visually-hidden color-radio"
                       {{ $colorActual === $hex ? 'checked' : '' }}>
                <span class="d-inline-block rounded-circle border border-2"
                      style="width:26px;height:26px;background:{{ $hex }};transition:transform .1s;border-color:{{ $colorActual === $hex ? '#000' : 'transparent' }} !important;"
                      data-hex="{{ $hex }}"></span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Fechas --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Fecha de Inicio</label>
        <input type="date" name="fecha_inicio" class="form-control"
               value="{{ old('fecha_inicio', optional($proyecto->fecha_inicio ?? null)->format('Y-m-d') ?? '') }}">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Fecha Límite</label>
        <input type="date" name="fecha_limite" class="form-control @error('fecha_limite') is-invalid @enderror"
               value="{{ old('fecha_limite', optional($proyecto->fecha_limite ?? null)->format('Y-m-d') ?? '') }}">
        @error('fecha_limite')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Miembros --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Miembros del Proyecto</label>
        <div class="border rounded p-3" style="max-height:220px;overflow-y:auto;">
            @foreach($usuarios as $usuario)
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="miembros[]"
                       id="miembro_{{ $usuario->id }}" value="{{ $usuario->id }}"
                       {{ in_array($usuario->id, old('miembros', $miembrosIds ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="miembro_{{ $usuario->id }}">
                    {{ $usuario->nombre ?? $usuario->name }}
                    <small class="text-muted ms-1">{{ $usuario->puesto ?? '' }}</small>
                </label>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.color-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.color-radio + span').forEach(function(span) {
            span.style.borderColor = 'transparent';
            span.style.transform = 'scale(1)';
        });
        if (this.checked) {
            var span = this.nextElementSibling;
            span.style.borderColor = '#000';
            span.style.transform = 'scale(1.2)';
        }
    });
    // Inicializar el estado visual del radio ya seleccionado
    if (radio.checked) {
        var span = radio.nextElementSibling;
        span.style.borderColor = '#000';
        span.style.transform = 'scale(1.2)';
    }
});
</script>
