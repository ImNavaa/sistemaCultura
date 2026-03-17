@extends('layouts.app')
@section('title', 'Editar Empleado')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Editar Empleado</h2>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($usuario->tiene_acceso)
<div class="alert alert-primary">
    <i class="bi bi-shield-check"></i> Este empleado tiene acceso al sistema.
</div>
@else
<div class="alert alert-secondary">
    <i class="bi bi-person"></i> Este empleado no tiene acceso al sistema.
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" id="formEmpleado">
            @csrf
            @method('PUT')
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
                    <div class="form-text" id="telefonoFeedback"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" id="cargo" class="form-control"
                        value="{{ old('cargo', $usuario->cargo) }}">
                </div>

                {{-- HORARIO con selectores --}}
                @php
                    $horarioActual  = old('horario', $usuario->horario ?? '');
                    $partes         = explode(' - ', $horarioActual);
                    $entradaActual  = trim($partes[0] ?? '');
                    $salidaActual   = trim($partes[1] ?? '');
                @endphp
                <div class="col-md-6">
                    <label class="form-label">Horario</label>
                    <div class="row g-1">
                        <div class="col-5">
                            <select name="hora_entrada_laboral" id="hora_entrada_laboral" class="form-select">
                                <option value="">Entrada</option>
                                @foreach(['07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','12:00','13:00','14:00','15:00','16:00'] as $h)
                                    <option value="{{ $h }}" {{ $entradaActual == $h ? 'selected' : '' }}>
                                        {{ $h }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 text-center pt-2 text-muted">—</div>
                        <div class="col-5">
                            <select name="hora_salida_laboral" id="hora_salida_laboral" class="form-select">
                                <option value="">Salida</option>
                                @foreach(['13:00','14:00','15:00','16:00','17:00','17:30','18:00','19:00','20:00','21:00','22:00'] as $h)
                                    <option value="{{ $h }}" {{ $salidaActual == $h ? 'selected' : '' }}>
                                        {{ $h }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="horario" id="horarioHidden" value="{{ $horarioActual }}">
                </div>

                {{-- DÍAS LABORALES con botones --}}
                @php
                    $diasSemana    = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo'];
                    $diasGuardados = explode(', ', old('dias_laborales', $usuario->dias_laborales ?? ''));
                @endphp
                <div class="col-md-12">
                    <label class="form-label">Días Laborales</label>
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

                @if($usuario->tiene_acceso)
                <div class="col-12"><hr><h6 class="text-primary">Datos de acceso al sistema</h6></div>

                <div class="col-md-12">
                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $usuario->email) }}">
                    <div class="form-text" id="emailFeedback"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nueva contraseña
                        <small class="text-muted">(dejar vacío para no cambiar)</small>
                    </label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Mínimo 8 caracteres">
                    <div class="progress mt-1" style="height:5px">
                        <div class="progress-bar" id="barraFortaleza" style="width:0%"></div>
                    </div>
                    <div class="form-text" id="fortalezaTexto"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control" placeholder="Repite la nueva contraseña">
                    <div class="form-text" id="confirmFeedback"></div>
                </div>
                @endif

                <div class="col-12">
                    <button type="submit" class="btn btn-warning" id="btnGuardar">
                        <i class="bi bi-save"></i> Actualizar Empleado
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const tieneAcceso = {{ $usuario->tiene_acceso ? 'true' : 'false' }};
    const userId      = {{ $usuario->id }};

    // ✅ Capitalizar nombre
    const campoName = document.getElementById('name');
    if (campoName) {
        campoName.addEventListener('input', function() {
            const pos = this.selectionStart;
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            this.setSelectionRange(pos, pos);
        });
    }

    // ✅ Capitalizar cargo
    const campoCargo = document.getElementById('cargo');
    if (campoCargo) {
        campoCargo.addEventListener('input', function() {
            const pos = this.selectionStart;
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            this.setSelectionRange(pos, pos);
        });
    }

    // ✅ Solo números y guiones en teléfono
    const campoTel = document.getElementById('telefono');
    if (campoTel) {
        campoTel.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9\-\s]/g, '');
        });
    }

    // ✅ Campos CON acceso
    if (tieneAcceso) {

        let emailTimer;
        const campoEmail = document.getElementById('email');
        if (campoEmail) {
            campoEmail.addEventListener('input', function() {
                clearTimeout(emailTimer);
                const email    = this.value;
                const feedback = document.getElementById('emailFeedback');

                if (!email.includes('@')) {
                    if (feedback) feedback.innerHTML = '<span class="text-danger">⚠️ Formato inválido.</span>';
                    return;
                }

                if (feedback) feedback.innerHTML = '<span class="text-muted">Verificando...</span>';

                emailTimer = setTimeout(function() {
                    fetch(`/usuarios/verificar-email?email=${encodeURIComponent(email)}&id=${userId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!feedback) return;
                            if (data.existe) {
                                feedback.innerHTML = '<span class="text-danger">❌ Email ya registrado.</span>';
                                campoEmail.classList.add('is-invalid');
                            } else {
                                feedback.innerHTML = '<span class="text-success">✅ Email disponible.</span>';
                                campoEmail.classList.remove('is-invalid');
                            }
                        })
                        .catch(function() { if (feedback) feedback.innerHTML = ''; });
                }, 600);
            });
        }

        const campoPass = document.getElementById('password');
        if (campoPass) {
            campoPass.addEventListener('input', function() {
                const val    = this.value;
                const barra  = document.getElementById('barraFortaleza');
                const texto  = document.getElementById('fortalezaTexto');
                let fortaleza = 0;

                if (val.length >= 8)           fortaleza++;
                if (/[A-Z]/.test(val))         fortaleza++;
                if (/[0-9]/.test(val))         fortaleza++;
                if (/[^A-Za-z0-9]/.test(val))  fortaleza++;

                const niveles = [
                    { pct: '25%',  color: 'bg-danger',  label: '❌ Muy débil' },
                    { pct: '50%',  color: 'bg-warning',  label: '⚠️ Débil' },
                    { pct: '75%',  color: 'bg-info',     label: '🔵 Aceptable' },
                    { pct: '100%', color: 'bg-success',  label: '✅ Fuerte' },
                ];

                if (val.length === 0) {
                    if (barra) barra.style.width = '0%';
                    if (texto) texto.innerHTML   = '';
                    return;
                }

                const nivel = niveles[fortaleza - 1] || niveles[0];
                if (barra) { barra.style.width = nivel.pct; barra.className = `progress-bar ${nivel.color}`; }
                if (texto) texto.innerHTML = nivel.label;
                verificarConfirmacion();
            });
        }

        const campoConfirm = document.getElementById('password_confirmation');
        if (campoConfirm) campoConfirm.addEventListener('input', verificarConfirmacion);
    }

    function verificarConfirmacion() {
        const pass     = document.getElementById('password');
        const confirm  = document.getElementById('password_confirmation');
        const feedback = document.getElementById('confirmFeedback');

        if (!pass || !confirm || !feedback) return;
        if (confirm.value.length === 0 || pass.value.length === 0) {
            feedback.innerHTML = '';
            return;
        }

        if (pass.value === confirm.value) {
            feedback.innerHTML = '<span class="text-success">✅ Las contraseñas coinciden.</span>';
            confirm.classList.remove('is-invalid');
        } else {
            feedback.innerHTML = '<span class="text-danger">❌ Las contraseñas no coinciden.</span>';
            confirm.classList.add('is-invalid');
        }
    }

    // ✅ Submit — combinar horario y días antes de enviar
    const form = document.getElementById('formEmpleado');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnGuardar');

            // Combinar horario
            const entrada = document.getElementById('hora_entrada_laboral')?.value;
            const salida  = document.getElementById('hora_salida_laboral')?.value;
            if (entrada && salida) {
                document.getElementById('horarioHidden').value = entrada + ' - ' + salida;
            }

            // Combinar días seleccionados
            const diasChecked = [...document.querySelectorAll('input[name="dias_check[]"]:checked')]
                .map(cb => cb.value);
            document.getElementById('diasHidden').value = diasChecked.join(', ');

            if (tieneAcceso) {
                const pass    = document.getElementById('password');
                const confirm = document.getElementById('password_confirmation');

                if (pass && confirm && pass.value && pass.value !== confirm.value) {
                    e.preventDefault();
                    const fb = document.getElementById('confirmFeedback');
                    if (fb) fb.innerHTML = '<span class="text-danger">❌ Las contraseñas no coinciden.</span>';
                    return;
                }
            }

            if (btn) {
                btn.disabled  = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
            }
        });
    }

});
</script>
@endsection