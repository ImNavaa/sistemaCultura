@extends('layouts.app')
@section('title', 'Editar — ' . $usuario->name)
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon amber"><i class="bi bi-pencil"></i></div>
        <div>
            <h2>Editar Empleado</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Empleados</a></li>
                    <li class="breadcrumb-item active">{{ $usuario->name }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('error') }}
    </div>
@endif

<form action="{{ route('usuarios.update', $usuario) }}" method="POST" id="formEmpleado">
    @csrf @method('PUT')

    <div class="row g-3">

        {{-- ── Datos del empleado ── --}}
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bi bi-person me-2"></i>Información del empleado
                </div>
                <div class="form-card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $usuario->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                value="{{ old('telefono', $usuario->telefono) }}"
                                placeholder="Ej: 492-123-4567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cargo</label>
                            <input type="text" name="cargo" id="cargo" class="form-control"
                                value="{{ old('cargo', $usuario->cargo) }}">
                        </div>

                        {{-- Horario --}}
                        @php
                            $horarioActual = old('horario', $usuario->horario ?? '');
                            $partes        = explode(' - ', $horarioActual);
                            $entradaActual = trim($partes[0] ?? '');
                            $salidaActual  = trim($partes[1] ?? '');
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label">Horario</label>
                            <div class="row g-1 align-items-center">
                                <div class="col-5">
                                    <select name="hora_entrada_laboral" id="hora_entrada_laboral" class="form-select">
                                        <option value="">Entrada</option>
                                        @foreach(['07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','12:00','13:00','14:00','15:00','16:00'] as $h)
                                            <option value="{{ $h }}" {{ $entradaActual == $h ? 'selected' : '' }}>{{ $h }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 text-center text-muted">—</div>
                                <div class="col-5">
                                    <select name="hora_salida_laboral" id="hora_salida_laboral" class="form-select">
                                        <option value="">Salida</option>
                                        @foreach(['13:00','14:00','15:00','16:00','17:00','17:30','18:00','19:00','20:00','21:00','22:00'] as $h)
                                            <option value="{{ $h }}" {{ $salidaActual == $h ? 'selected' : '' }}>{{ $h }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="horario" id="horarioHidden" value="{{ $horarioActual }}">
                        </div>

                        {{-- Días laborales --}}
                        @php
                            $diasSemana    = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo'];
                            $diasGuardados = explode(', ', old('dias_laborales', $usuario->dias_laborales ?? ''));
                        @endphp
                        <div class="col-12">
                            <label class="form-label">Días laborales</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($diasSemana as $dia)
                                <div>
                                    <input type="checkbox" class="btn-check" name="dias_check[]"
                                           id="dia_{{ $loop->index }}" value="{{ $dia }}"
                                           {{ in_array($dia, $diasGuardados) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="dia_{{ $loop->index }}">
                                        {{ substr($dia, 0, 3) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="dias_laborales" id="diasHidden"
                                   value="{{ old('dias_laborales', $usuario->dias_laborales ?? '') }}">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ── Acceso al sistema ── --}}
        <div class="col-12">
            <div class="form-card">
                <div class="form-card-header">
                    <i class="bi bi-shield-lock me-2"></i>Acceso al sistema
                </div>
                <div class="form-card-body">

                    {{-- Toggle --}}
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded"
                         style="background:#f8f9fc;border:1px solid #e8eaf6;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="toggleAcceso"
                                   {{ old('tiene_acceso', $usuario->tiene_acceso) ? 'checked' : '' }}
                                   style="width:3rem;height:1.5rem;cursor:pointer;">
                            <input type="hidden" name="tiene_acceso" id="tieneAccesoHidden"
                                   value="{{ old('tiene_acceso', $usuario->tiene_acceso) ? '1' : '0' }}">
                        </div>
                        <div>
                            <div id="accesoLabel" class="fw-semibold" style="font-size:.95rem;">
                                {{ $usuario->tiene_acceso ? 'Con acceso al sistema' : 'Sin acceso al sistema' }}
                            </div>
                            <div class="text-muted" style="font-size:.8rem;">
                                {{ $usuario->tiene_acceso
                                    ? 'El empleado puede iniciar sesión con email y contraseña.'
                                    : 'El empleado no puede iniciar sesión en el sistema.' }}
                            </div>
                        </div>
                        @if($usuario->id === auth()->id() || $usuario->rol?->nombre === 'super_admin')
                            <span class="badge ms-auto" style="background:#e8eaf6;color:var(--navy);font-size:.78rem;">
                                <i class="bi bi-lock me-1"></i>No modificable
                            </span>
                        @endif
                    </div>

                    {{-- Campos de acceso (visibles cuando tiene_acceso = true) --}}
                    <div id="seccionAcceso" style="{{ old('tiene_acceso', $usuario->tiene_acceso) ? '' : 'display:none;' }}">
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="form-section-title">Credenciales de acceso</div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $usuario->email) }}"
                                    placeholder="correo@ejemplo.com">
                                <div class="form-text" id="emailFeedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Nueva contraseña
                                    <span class="text-muted fw-normal" id="passRequerida" style="font-size:.8rem;">
                                        {{ $usuario->tiene_acceso ? '(dejar vacío para no cambiar)' : '(requerida)' }}
                                    </span>
                                </label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Mínimo 8 caracteres">
                                <div class="progress mt-1" style="height:4px;">
                                    <div class="progress-bar" id="barraFortaleza" style="width:0%;border-radius:4px;"></div>
                                </div>
                                <div class="form-text" id="fortalezaTexto"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Repite la contraseña">
                                <div class="form-text" id="confirmFeedback"></div>
                            </div>

                        </div>
                    </div>

                    {{-- Advertencia cuando se va a revocar acceso --}}
                    <div id="alertaRevocar" class="mt-3" style="display:none;">
                        <div class="alert alert-danger mb-0" style="font-size:.88rem;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Atención:</strong> Al guardar, este empleado ya no podrá iniciar sesión en el sistema.
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-navy px-4" id="btnGuardar">
                <i class="bi bi-save me-1"></i> Guardar cambios
            </button>
        </div>

    </div>
</form>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tieneAccesoOriginal = {{ $usuario->tiene_acceso ? 'true' : 'false' }};
    const esProtegido         = {{ ($usuario->id === auth()->id() || $usuario->rol?->nombre === 'super_admin') ? 'true' : 'false' }};
    const userId              = {{ $usuario->id }};

    const toggle      = document.getElementById('toggleAcceso');
    const hidden      = document.getElementById('tieneAccesoHidden');
    const seccion     = document.getElementById('seccionAcceso');
    const alerta      = document.getElementById('alertaRevocar');
    const label       = document.getElementById('accesoLabel');
    const passHint    = document.getElementById('passRequerida');

    // Bloquear toggle si es usuario protegido
    if (esProtegido) toggle.disabled = true;

    toggle.addEventListener('change', function () {
        const activo = this.checked;
        hidden.value = activo ? '1' : '0';

        label.textContent = activo ? 'Con acceso al sistema' : 'Sin acceso al sistema';
        seccion.style.display = activo ? '' : 'none';

        // Mostrar advertencia solo si se va a revocar acceso que ya tenía
        alerta.style.display = (!activo && tieneAccesoOriginal) ? '' : 'none';

        // Actualizar hint de contraseña
        if (passHint) {
            passHint.textContent = (activo && !tieneAccesoOriginal)
                ? '(requerida)'
                : '(dejar vacío para no cambiar)';
        }
    });

    // ── Capitalizar nombre y cargo ──
    ['name', 'cargo'].forEach(id => {
        const campo = document.getElementById(id);
        if (!campo) return;
        campo.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            this.setSelectionRange(pos, pos);
        });
    });

    // ── Teléfono solo números ──
    const tel = document.getElementById('telefono');
    if (tel) tel.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9\-\s]/g, '');
    });

    // ── Verificar email ──
    let emailTimer;
    const campoEmail = document.getElementById('email');
    if (campoEmail) {
        campoEmail.addEventListener('input', function () {
            clearTimeout(emailTimer);
            const email    = this.value;
            const feedback = document.getElementById('emailFeedback');
            if (!email.includes('@')) {
                feedback.innerHTML = '<span class="text-danger small">Formato inválido.</span>';
                return;
            }
            feedback.innerHTML = '<span class="text-muted small">Verificando...</span>';
            emailTimer = setTimeout(() => {
                fetch(`/usuarios/verificar-email?email=${encodeURIComponent(email)}&id=${userId}`)
                    .then(r => r.json())
                    .then(data => {
                        feedback.innerHTML = data.existe
                            ? '<span class="text-danger small">Email ya registrado.</span>'
                            : '<span class="text-success small">Email disponible.</span>';
                        campoEmail.classList.toggle('is-invalid', data.existe);
                    });
            }, 600);
        });
    }

    // ── Fortaleza de contraseña ──
    const campoPass = document.getElementById('password');
    if (campoPass) {
        campoPass.addEventListener('input', function () {
            const val     = this.value;
            const barra   = document.getElementById('barraFortaleza');
            const texto   = document.getElementById('fortalezaTexto');
            let nivel = 0;
            if (val.length >= 8)           nivel++;
            if (/[A-Z]/.test(val))         nivel++;
            if (/[0-9]/.test(val))         nivel++;
            if (/[^A-Za-z0-9]/.test(val))  nivel++;

            const cfg = [
                { w: '25%', color: 'bg-danger',  label: 'Muy débil' },
                { w: '50%', color: 'bg-warning',  label: 'Débil' },
                { w: '75%', color: 'bg-info',     label: 'Aceptable' },
                { w: '100%',color: 'bg-success',  label: 'Fuerte' },
            ];
            if (!val) { barra.style.width = '0%'; texto.innerHTML = ''; return; }
            const c = cfg[(nivel - 1)] || cfg[0];
            barra.style.width = c.w;
            barra.className   = `progress-bar ${c.color}`;
            texto.innerHTML   = `<span class="small">${c.label}</span>`;
            verificarConfirmacion();
        });
    }

    const campoConf = document.getElementById('password_confirmation');
    if (campoConf) campoConf.addEventListener('input', verificarConfirmacion);

    function verificarConfirmacion() {
        const pass     = document.getElementById('password');
        const confirm  = document.getElementById('password_confirmation');
        const feedback = document.getElementById('confirmFeedback');
        if (!pass || !confirm || !feedback) return;
        if (!confirm.value || !pass.value) { feedback.innerHTML = ''; return; }
        const ok = pass.value === confirm.value;
        feedback.innerHTML = ok
            ? '<span class="text-success small">Las contraseñas coinciden.</span>'
            : '<span class="text-danger small">Las contraseñas no coinciden.</span>';
        confirm.classList.toggle('is-invalid', !ok);
    }

    // ── Submit ──
    document.getElementById('formEmpleado').addEventListener('submit', function (e) {
        const btn = document.getElementById('btnGuardar');

        // Combinar horario
        const entrada = document.getElementById('hora_entrada_laboral')?.value;
        const salida  = document.getElementById('hora_salida_laboral')?.value;
        if (entrada && salida) {
            document.getElementById('horarioHidden').value = entrada + ' - ' + salida;
        }

        // Combinar días
        const dias = [...document.querySelectorAll('input[name="dias_check[]"]:checked')].map(c => c.value);
        document.getElementById('diasHidden').value = dias.join(', ');

        // Validar contraseñas
        const pass    = document.getElementById('password');
        const confirm = document.getElementById('password_confirmation');
        if (pass && confirm && pass.value && pass.value !== confirm.value) {
            e.preventDefault();
            document.getElementById('confirmFeedback').innerHTML =
                '<span class="text-danger small">Las contraseñas no coinciden.</span>';
            return;
        }

        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Guardando...'; }
    });

});
</script>
@endsection
