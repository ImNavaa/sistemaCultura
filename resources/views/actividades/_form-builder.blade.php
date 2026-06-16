@php
$config = isset($actividad) ? $actividad->configFormulario() : [
    'campos' => [
        'email' => 'opcional', 'telefono' => 'opcional', 'edad' => 'opcional',
        'genero' => 'opcional', 'institucion' => 'opcional', 'ocupacion' => 'opcional',
        'ciudad' => 'opcional', 'curp' => 'oculto',
    ],
    'preguntas_extra' => [],
];

$camposLabels = [
    'email'          => ['Correo electrónico',   'bi-envelope'],
    'telefono'       => ['Teléfono',             'bi-telephone'],
    'edad'           => ['Edad',                 'bi-person-badge'],
    'genero'         => ['Género',               'bi-gender-ambiguous'],
    'institucion'    => ['Institución / Empresa','bi-building'],
    'ocupacion'      => ['Ocupación',            'bi-briefcase'],
    'ciudad'         => ['Ciudad',               'bi-geo-alt'],
    'redes_sociales' => ['Redes sociales',       'bi-instagram'],
    'curp'           => ['CURP',                 'bi-card-text'],
];
@endphp

<div class="col-12 mt-2">
    <div class="form-section-title">
        <i class="bi bi-ui-checks me-1"></i>Formulario de registro público
    </div>
    <p class="text-muted small mb-3">Define qué datos se pedirán cuando alguien se registre desde el enlace público.</p>
</div>

{{-- Campos estándar --}}
<div class="col-12">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" style="background:var(--bg-card-alt);border-radius:10px;overflow:hidden;">
            <thead>
                <tr style="background:var(--bg-card);font-size:.8rem;color:var(--text-muted);">
                    <th style="padding:.6rem 1rem;font-weight:600;">Campo</th>
                    <th class="text-center" style="width:110px;padding:.6rem;">
                        <span class="badge bg-danger" style="font-size:.72rem;">Requerido</span>
                    </th>
                    <th class="text-center" style="width:110px;padding:.6rem;">
                        <span class="badge bg-success" style="font-size:.72rem;">Opcional</span>
                    </th>
                    <th class="text-center" style="width:110px;padding:.6rem;">
                        <span class="badge bg-secondary" style="font-size:.72rem;">Oculto</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr style="background:var(--bg-card);opacity:.65;">
                    <td style="padding:.55rem 1rem;font-size:.85rem;">
                        <i class="bi bi-person me-2 text-muted"></i><strong>Nombre(s)</strong>
                    </td>
                    <td class="text-center" colspan="3">
                        <span class="badge bg-danger" style="font-size:.72rem;">Siempre requerido</span>
                    </td>
                </tr>
                <tr style="background:var(--bg-card);opacity:.65;">
                    <td style="padding:.55rem 1rem;font-size:.85rem;">
                        <i class="bi bi-person me-2 text-muted"></i><strong>Apellidos</strong>
                    </td>
                    <td class="text-center" colspan="3">
                        <span class="badge bg-danger" style="font-size:.72rem;">Siempre requerido</span>
                    </td>
                </tr>
                @foreach($camposLabels as $campo => [$label, $icon])
                @php $estado = $config['campos'][$campo] ?? 'opcional'; @endphp
                <tr class="campo-row" style="border-top:1px solid var(--border-color);">
                    <td style="padding:.55rem 1rem;font-size:.85rem;">
                        <i class="bi {{ $icon }} me-2 text-muted"></i>{{ $label }}
                    </td>
                    @foreach(['requerido', 'opcional', 'oculto'] as $opcion)
                    <td class="text-center" style="padding:.55rem;">
                        <div class="form-check d-flex justify-content-center m-0">
                            <input class="form-check-input" type="radio"
                                   name="campo_{{ $campo }}"
                                   value="{{ $opcion }}"
                                   id="campo_{{ $campo }}_{{ $opcion }}"
                                   {{ $estado === $opcion ? 'checked' : '' }}>
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Preguntas extra --}}
<div class="col-12 mt-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div style="font-size:.85rem;font-weight:600;color:var(--text-label);">
            <i class="bi bi-question-circle me-1"></i>Preguntas adicionales
            <span class="text-muted fw-normal" style="font-size:.78rem;">(hasta 5 preguntas personalizadas)</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarPregunta"
                onclick="agregarPregunta()">
            <i class="bi bi-plus me-1"></i>Agregar pregunta
        </button>
    </div>

    <div id="preguntasContainer">
        @foreach($config['preguntas_extra'] as $i => $pregunta)
        <div class="pregunta-item border rounded p-3 mb-2" style="background:var(--bg-card-alt);" data-index="{{ $i }}">
            <div class="row g-2 align-items-start">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-1">Pregunta</label>
                    <input type="text" name="pregunta_label[]" class="form-control form-control-sm"
                           value="{{ $pregunta['label'] }}" placeholder="Ej. ¿Cómo te enteraste?" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Tipo de respuesta</label>
                    <select name="pregunta_tipo[]" class="form-select form-select-sm tipo-select"
                            onchange="toggleOpciones(this)">
                        <option value="texto"       {{ ($pregunta['tipo'] ?? 'texto') === 'texto'       ? 'selected' : '' }}>Texto libre</option>
                        <option value="texto_largo" {{ ($pregunta['tipo'] ?? '') === 'texto_largo' ? 'selected' : '' }}>Texto largo</option>
                        <option value="seleccion"   {{ ($pregunta['tipo'] ?? '') === 'seleccion'   ? 'selected' : '' }}>Selección</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">¿Requerida?</label>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="pregunta_requerido[{{ $i }}]"
                               {{ ($pregunta['requerido'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label small">Sí</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarPregunta(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="col-12 opciones-container {{ ($pregunta['tipo'] ?? 'texto') !== 'seleccion' ? 'd-none' : '' }}">
                    <label class="form-label small fw-semibold mb-1">
                        Opciones <span class="text-muted fw-normal">(una por línea)</span>
                    </label>
                    <textarea name="pregunta_opciones[]" class="form-control form-control-sm" rows="3"
                              placeholder="Redes sociales&#10;Amigo/familiar&#10;Cartel&#10;Otro">{{ implode("\n", $pregunta['opciones'] ?? []) }}</textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<template id="tplPregunta">
    <div class="pregunta-item border rounded p-3 mb-2" style="background:var(--bg-card-alt);">
        <div class="row g-2 align-items-start">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Pregunta</label>
                <input type="text" name="pregunta_label[]" class="form-control form-control-sm"
                       placeholder="Ej. ¿Cómo te enteraste?" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Tipo de respuesta</label>
                <select name="pregunta_tipo[]" class="form-select form-select-sm tipo-select"
                        onchange="toggleOpciones(this)">
                    <option value="texto">Texto libre</option>
                    <option value="texto_largo">Texto largo</option>
                    <option value="seleccion">Selección</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">¿Requerida?</label>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="pregunta_requerido_tpl" disabled>
                    <label class="form-check-label small">Sí</label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarPregunta(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="col-12 opciones-container d-none">
                <label class="form-label small fw-semibold mb-1">
                    Opciones <span class="text-muted fw-normal">(una por línea)</span>
                </label>
                <textarea name="pregunta_opciones[]" class="form-control form-control-sm" rows="3"
                          placeholder="Redes sociales&#10;Amigo/familiar&#10;Cartel&#10;Otro"></textarea>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let preguntaCount = {{ count($config['preguntas_extra']) }};

function agregarPregunta() {
    if (preguntaCount >= 5) {
        alert('Máximo 5 preguntas adicionales.');
        return;
    }
    const tpl   = document.getElementById('tplPregunta');
    const clone = tpl.content.cloneNode(true);
    const div   = clone.querySelector('.pregunta-item');

    // Arreglar el name del checkbox para que se envíe correctamente
    const chk = div.querySelector('input[type="checkbox"]');
    chk.name     = `pregunta_requerido[${preguntaCount}]`;
    chk.disabled = false;

    document.getElementById('preguntasContainer').appendChild(div);
    preguntaCount++;
    actualizarBoton();
}

function eliminarPregunta(btn) {
    btn.closest('.pregunta-item').remove();
    preguntaCount = Math.max(0, preguntaCount - 1);
    // Re-indexar checkboxes de requerido
    document.querySelectorAll('.pregunta-item').forEach((el, i) => {
        const chk = el.querySelector('input[type="checkbox"]');
        if (chk) chk.name = `pregunta_requerido[${i}]`;
    });
    actualizarBoton();
}

function toggleOpciones(select) {
    const container = select.closest('.pregunta-item').querySelector('.opciones-container');
    container.classList.toggle('d-none', select.value !== 'seleccion');
}

function actualizarBoton() {
    const btn = document.getElementById('btnAgregarPregunta');
    if (btn) btn.disabled = preguntaCount >= 5;
}

actualizarBoton();
</script>
@endpush
