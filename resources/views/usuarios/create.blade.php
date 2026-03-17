@extends('layouts.app')
@section('title', $tipo === 'con_acceso' ? 'Nuevo Empleado con Acceso' : 'Nuevo Empleado sin Acceso')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-person-plus"></i>
        {{ $tipo === 'con_acceso' ? 'Nuevo Empleado con Acceso' : 'Nuevo Empleado sin Acceso' }}
    </h2>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if($tipo === 'con_acceso')
<div class="alert alert-primary">
    <i class="bi bi-info-circle"></i> Este empleado podrá iniciar sesión en el sistema.
</div>
@else
<div class="alert alert-secondary">
    <i class="bi bi-info-circle"></i> Este empleado solo aparecerá en el directorio, sin acceso al sistema.
</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST" id="formEmpleado">
            @csrf
            <input type="hidden" name="tiene_acceso" value="{{ $tipo === 'con_acceso' ? '1' : '0' }}">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="Nombre del empleado" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono"
                        class="form-control @error('telefono') is-invalid @enderror"
                        value="{{ old('telefono') }}" placeholder="Ej: 492-123-4567" maxlength="15">
                    <div class="form-text" id="telefonoFeedback"></div>
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="cargo" id="cargo" class="form-control"
                        value="{{ old('cargo') }}" placeholder="Ej: Técnico de sonido">
                </div>

                {{-- HORARIO con selectores --}}
                <div class="col-md-6">
                    <label class="form-label">Horario</label>
                    <div class="row g-1">
                        <div class="col-5">
                            <select name="hora_entrada_laboral" id="hora_entrada_laboral" class="form-select">
                                <option value="">Entrada</option>
                                @foreach(['07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','12:00','13:00','14:00','15:00','16:00'] as $h)
                                    <option value="{{ $h }}" {{ old('hora_entrada_laboral') == $h ? 'selected' : '' }}>
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
                                    <option value="{{ $h }}" {{ old('hora_salida_laboral') == $h ? 'selected' : '' }}>
                                        {{ $h }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="horario" id="horarioHidden" value="{{ old('horario') }}">
                </div>

                {{-- DÍAS LABORALES con botones --}}
                <div class="col-md-12">
                    <label class="form-label">Días Laborales</label>
                    @php
                        $diasSemana   = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                        $diasGuardados = explode(', ', old('dias_laborales', ''));
                    @endphp
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
                    <input type="hidden" name="dias_laborales" id="diasHidden" value="{{ old('dias_laborales') }}">
                    <div class="form-text" id="diasFeedback"></div>
                </div>

                {{-- Campos CON acceso --}}
                @if($tipo === 'con_acceso')
                <div class="col-12"><hr><h6 class="text-primary">Datos de acceso al sistema</h6></div>

                <div class="col-md-12">
                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                    <div class="form-text" id="emailFeedback"></div>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Mínimo 8 caracteres">
                    <div class="progress mt-1" style="height:5px">
                        <div class="progress-bar" id="barraFortaleza" style="width:0%"></div>
                    </div>
                    <div class="form-text" id="fortalezaTexto"></div>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control" placeholder="Repite la contraseña">
                    <div class="form-text" id="confirmFeedback"></div>
                </div>
                @endif

                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Registrar Empleado
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

    const tieneAcceso = {{ $tipo === 'con_acceso' ? 'true' : 'false' }};

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
            const feedback = document.getElementById('telefonoFeedback');
            if (feedback) {
                if (this.value.length > 0 && this.value.replace(/\D/g, '').length < 10) {
                    feedback.innerHTML = '<span class="text-warning">⚠️ Mínimo 10 dígitos.</span>';
                } else {
                    feedback.innerHTML = '';
                }
            }
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
                    fetch(`/usuarios/verificar-email?email=${encodeURIComponent(email)}`)
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
        if (confirm.value.length === 0) { feedback.innerHTML = ''; return; }

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

            if (diasChecked.length === 0) {
                // Días opcionales — no bloquear si no seleccionó
                document.getElementById('diasHidden').value = '';
            } else {
                document.getElementById('diasHidden').value = diasChecked.join(', ');
            }

            if (tieneAcceso) {
                const pass    = document.getElementById('password');
                const confirm = document.getElementById('password_confirmation');
                const email   = document.getElementById('email');

                if (pass && confirm && pass.value !== confirm.value) {
                    e.preventDefault();
                    const fb = document.getElementById('confirmFeedback');
                    if (fb) fb.innerHTML = '<span class="text-danger">❌ Las contraseñas no coinciden.</span>';
                    return;
                }

                if (email && email.classList.contains('is-invalid')) {
                    e.preventDefault();
                    const fb = document.getElementById('emailFeedback');
                    if (fb) fb.innerHTML = '<span class="text-danger">❌ Corrige el email.</span>';
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