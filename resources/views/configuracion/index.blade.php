@extends('layouts.app')
@section('title', 'Configuración')

@push('styles')
<style>
/* ── Avatar ── */
.cfg-avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--navy), var(--navy3));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 700;
    margin: 0 auto;
    flex-shrink: 0;
}

/* ── Opciones de tema ── */
.tema-opcion {
    border: 2px solid var(--border-color);
    border-radius: 14px;
    overflow: hidden;
    background: var(--bg-card-alt);
    cursor: pointer;
    padding: 0;
    width: 100%;
    transition: border-color .2s, box-shadow .2s, transform .15s;
    text-align: left;
}
.tema-opcion:hover {
    transform: translateY(-3px);
    border-color: var(--navy3);
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
}
.tema-opcion.activo {
    border-color: var(--navy3);
    box-shadow: 0 0 0 3px rgba(15,52,96,.18);
}
[data-theme="dark"] .tema-opcion.activo {
    box-shadow: 0 0 0 3px rgba(92,107,192,.3);
}

/* ── Mini preview ── */
.prev-wrap { height: 96px; overflow: hidden; }
.prev-wrap.claro  { background: #e8eaed; }
.prev-wrap.oscuro { background: #0b0c14; }
.prev-navbar { height: 20px; width: 100%; display: flex; align-items: center; gap: 5px; padding: 0 8px; }
.prev-wrap.claro  .prev-navbar { background: #1a1a2e; }
.prev-wrap.oscuro .prev-navbar { background: #08080f; }
.prev-dot { width: 5px; height: 5px; border-radius: 50%; background: rgba(255,255,255,.4); }
.prev-dot.accent { background: #e94560; }
.prev-body { display: flex; gap: 5px; padding: 6px; }
.prev-card { border-radius: 5px; flex: 1; height: 56px; }
.prev-wrap.claro  .prev-card { background: #ffffff; border: 1px solid #e5e7eb; }
.prev-wrap.oscuro .prev-card { background: #1e1e2e; border: 1px solid #2a2a40; }
.prev-card-line { height: 5px; border-radius: 3px; margin: 9px 8px 5px; }
.prev-wrap.claro  .prev-card-line { background: #e0e3e8; }
.prev-wrap.oscuro .prev-card-line { background: #3a3a55; }
.prev-card-line.short { width: 50%; margin-top: 4px; }
.prev-wrap.claro  .prev-card-line.short { background: #eaedf0; }
.prev-wrap.oscuro .prev-card-line.short { background: #2d2d45; }

/* ── Label del tema ── */
.tema-label {
    padding: .65rem 1rem;
    font-size: .9rem;
    font-weight: 600;
    color: var(--text-main);
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid var(--border-color);
}
.tema-label .tema-nombre { display: flex; align-items: center; gap: .45rem; }
.tema-check {
    color: var(--navy3);
    font-size: 1.1rem;
    opacity: 0;
    transition: opacity .2s, transform .2s;
    transform: scale(.6);
}
[data-theme="dark"] .tema-check { color: #7986cb; }
.tema-opcion.activo .tema-check { opacity: 1; transform: scale(1); }

/* ── Sección título ── */
.cfg-section-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--text-muted);
    margin-bottom: .75rem;
    padding-bottom: .4rem;
    border-bottom: 1px solid var(--border-color);
}

/* ── Fortaleza de contraseña ── */
.pass-strength { height: 4px; border-radius: 4px; transition: width .3s, background .3s; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon navy"><i class="bi bi-gear-fill"></i></div>
        <div>
            <h2>Configuración</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Configuración</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── Columna izquierda: Mi cuenta ── --}}
    <div class="col-12 col-lg-4">
        <div class="data-card">
            <div class="data-card-header">
                <div class="header-icon navy"><i class="bi bi-person-fill"></i></div>
                Mi cuenta
            </div>
            <div class="p-4 text-center" style="border-bottom:1px solid var(--border-color);">
                <div class="cfg-avatar mb-3">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <h5 class="fw-bold mb-1" style="color:var(--text-main);">{{ auth()->user()->name }}</h5>
                @if(auth()->user()->cargo)
                    <p class="mb-2" style="color:var(--text-muted);font-size:.88rem;">{{ auth()->user()->cargo }}</p>
                @endif
                @if(auth()->user()->rol)
                    <span class="badge" style="background:var(--bg-card-alt);color:var(--text-main);border:1px solid var(--border-color);font-size:.78rem;padding:.35rem .75rem;border-radius:20px;">
                        <i class="bi bi-shield-check me-1"></i>{{ ucfirst(str_replace('_', ' ', auth()->user()->rol->nombre)) }}
                    </span>
                @endif
            </div>
            <ul class="info-list px-4 py-2">
                @if(auth()->user()->email)
                <li>
                    <span class="info-key"><i class="bi bi-envelope me-1"></i>Email</span>
                    <span class="info-val" style="font-size:.85rem;word-break:break-all;">{{ auth()->user()->email }}</span>
                </li>
                @endif
                @if(auth()->user()->horario)
                <li>
                    <span class="info-key"><i class="bi bi-clock me-1"></i>Horario</span>
                    <span class="info-val">{{ auth()->user()->horario }}</span>
                </li>
                @endif
                @if(auth()->user()->dias_laborales)
                <li>
                    <span class="info-key"><i class="bi bi-calendar-week me-1"></i>Días</span>
                    <span class="info-val" style="font-size:.85rem;">{{ auth()->user()->dias_laborales }}</span>
                </li>
                @endif
                @if(auth()->user()->telefono)
                <li>
                    <span class="info-key"><i class="bi bi-telephone me-1"></i>Teléfono</span>
                    <span class="info-val">{{ auth()->user()->telefono }}</span>
                </li>
                @endif
            </ul>
        </div>
    </div>

    {{-- ── Columna derecha: Apariencia + Contraseña ── --}}
    <div class="col-12 col-lg-8 d-flex flex-column gap-4">

        {{-- ── Apariencia ── --}}
        <div class="data-card">
            <div class="data-card-header">
                <div class="header-icon amber"><i class="bi bi-palette-fill"></i></div>
                Apariencia
            </div>
            <div class="p-4">
                <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:1.25rem;">
                    Elige cómo quieres ver el sistema. La preferencia se guarda en este dispositivo.
                </p>

                <div class="cfg-section-title">Tema de color</div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <button class="tema-opcion" id="btnClaro" data-tema="claro">
                            <div class="prev-wrap claro">
                                <div class="prev-navbar">
                                    <div class="prev-dot accent"></div>
                                    <div class="prev-dot"></div>
                                    <div class="prev-dot"></div>
                                </div>
                                <div class="prev-body">
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                </div>
                            </div>
                            <div class="tema-label">
                                <span class="tema-nombre"><i class="bi bi-sun-fill" style="color:#f59e0b;"></i> Modo Claro</span>
                                <i class="bi bi-check-circle-fill tema-check"></i>
                            </div>
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="tema-opcion" id="btnOscuro" data-tema="oscuro">
                            <div class="prev-wrap oscuro">
                                <div class="prev-navbar">
                                    <div class="prev-dot accent"></div>
                                    <div class="prev-dot"></div>
                                    <div class="prev-dot"></div>
                                </div>
                                <div class="prev-body">
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                    <div class="prev-card"><div class="prev-card-line"></div><div class="prev-card-line short"></div></div>
                                </div>
                            </div>
                            <div class="tema-label">
                                <span class="tema-nombre"><i class="bi bi-moon-fill" style="color:#7986cb;"></i> Modo Oscuro</span>
                                <i class="bi bi-check-circle-fill tema-check"></i>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 p-3 rounded" style="background:var(--bg-card-alt);border:1px solid var(--border-color);">
                    <i class="bi bi-info-circle" style="color:var(--text-muted);"></i>
                    <small style="color:var(--text-muted);">
                        Tema activo: <strong id="temaActivoTexto" style="color:var(--text-main);"></strong>
                    </small>
                </div>
            </div>
        </div>

        {{-- ── Cambiar contraseña ── --}}
        <div class="data-card" id="seccionPassword">
            <div class="data-card-header">
                <div class="header-icon teal"><i class="bi bi-lock-fill"></i></div>
                Cambiar contraseña
            </div>
            <div class="p-4">

                @if($errors->has('password_actual') && session('seccion') === 'password')
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('password_actual') }}
                </div>
                @endif

                @if($errors->has('password') && session('seccion') === 'password')
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('password') }}
                </div>
                @endif

                @if(!auth()->user()->tiene_acceso || !auth()->user()->email)
                <div class="d-flex align-items-center gap-2 p-3 rounded" style="background:var(--bg-card-alt);border:1px solid var(--border-color);">
                    <i class="bi bi-lock text-muted fs-5"></i>
                    <small style="color:var(--text-muted);">Este perfil no tiene acceso al sistema por credenciales, por lo que no es posible cambiar contraseña.</small>
                </div>
                @else

                <form action="{{ route('configuracion.password') }}" method="POST" id="formPassword" autocomplete="off">
                    @csrf
                    <div class="row g-3">

                        <div class="col-12">
                            <div class="cfg-section-title">Verificación de identidad</div>
                            <label class="form-label">Contraseña actual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_actual" id="password_actual"
                                    class="form-control @error('password_actual') is-invalid @enderror"
                                    placeholder="Tu contraseña actual" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVer('password_actual', this)" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_actual')
                                <div class="text-danger mt-1" style="font-size:.82rem;"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="cfg-section-title mt-2">Nueva contraseña</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password_nueva"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVer('password_nueva', this)" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="progress mt-2" style="height:4px;background:var(--border-color);">
                                <div class="pass-strength" id="barraFortaleza" style="width:0%;"></div>
                            </div>
                            <div id="textoFortaleza" style="font-size:.78rem;margin-top:3px;min-height:1.1em;color:var(--text-muted);"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirm"
                                    class="form-control"
                                    placeholder="Repite la nueva contraseña" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVer('password_confirm', this)" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="textoConfirm" style="font-size:.78rem;margin-top:3px;min-height:1.1em;"></div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 pt-1">
                            <button type="reset" class="btn btn-outline-secondary" onclick="resetFormPassword()">
                                <i class="bi bi-x-circle me-1"></i>Limpiar
                            </button>
                            <button type="submit" class="btn btn-navy px-4" id="btnCambiarPass">
                                <i class="bi bi-lock-fill me-1"></i>Cambiar contraseña
                            </button>
                        </div>

                    </div>
                </form>

                @endif
            </div>
        </div>

    </div>{{-- fin columna derecha --}}

</div>

@endsection

@section('scripts')
<script>
(function () {

    /* ── Tema ──────────────────────────────────────── */
    function aplicarTema(oscuro) {
        const html    = document.documentElement;
        const iconNav = document.getElementById('iconTema');
        const btnNav  = document.getElementById('btnTema');
        if (oscuro) {
            html.setAttribute('data-theme', 'dark');
            if (iconNav) iconNav.className = 'bi bi-sun-fill';
            if (btnNav)  btnNav.title      = 'Cambiar a modo claro';
        } else {
            html.removeAttribute('data-theme');
            if (iconNav) iconNav.className = 'bi bi-moon-fill';
            if (btnNav)  btnNav.title      = 'Cambiar a modo oscuro';
        }
    }

    function actualizarUITema(temaActivo) {
        document.querySelectorAll('.tema-opcion').forEach(btn => {
            btn.classList.toggle('activo', btn.dataset.tema === temaActivo);
        });
        const texto = document.getElementById('temaActivoTexto');
        if (texto) texto.textContent = temaActivo === 'oscuro' ? 'Modo Oscuro' : 'Modo Claro';
    }

    function elegirTema(tema) {
        localStorage.setItem('tema', tema);
        aplicarTema(tema === 'oscuro');
        actualizarUITema(tema);
    }

    actualizarUITema(localStorage.getItem('tema') || 'claro');
    document.getElementById('btnClaro')?.addEventListener('click',  () => elegirTema('claro'));
    document.getElementById('btnOscuro')?.addEventListener('click', () => elegirTema('oscuro'));

    /* ── Contraseña: mostrar/ocultar ────────────────── */
    window.toggleVer = function(id, btn) {
        const input = document.getElementById(id);
        if (!input) return;
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        btn.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    };

    /* ── Fortaleza de contraseña ────────────────────── */
    const passInput    = document.getElementById('password_nueva');
    const confirmInput = document.getElementById('password_confirm');

    if (passInput) {
        passInput.addEventListener('input', function () {
            const val  = this.value;
            const barra = document.getElementById('barraFortaleza');
            const texto = document.getElementById('textoFortaleza');
            let nivel = 0;
            if (val.length >= 8)           nivel++;
            if (/[A-Z]/.test(val))         nivel++;
            if (/[0-9]/.test(val))         nivel++;
            if (/[^A-Za-z0-9]/.test(val))  nivel++;

            const cfg = [
                { w: '25%',  bg: '#ef4444', label: 'Muy débil' },
                { w: '50%',  bg: '#f59e0b', label: 'Débil' },
                { w: '75%',  bg: '#3b82f6', label: 'Aceptable' },
                { w: '100%', bg: '#22c55e', label: 'Fuerte' },
            ];
            if (!val) {
                barra.style.width = '0%';
                texto.innerHTML = '';
                return;
            }
            const c = cfg[nivel - 1] || cfg[0];
            barra.style.width      = c.w;
            barra.style.background = c.bg;
            texto.innerHTML = `<span style="color:${c.bg}">${c.label}</span>`;
            verificarConfirm();
        });
    }

    if (confirmInput) confirmInput.addEventListener('input', verificarConfirm);

    function verificarConfirm() {
        const pass    = document.getElementById('password_nueva');
        const confirm = document.getElementById('password_confirm');
        const texto   = document.getElementById('textoConfirm');
        if (!pass || !confirm || !texto) return;
        if (!confirm.value) { texto.innerHTML = ''; return; }
        const ok = pass.value === confirm.value;
        texto.innerHTML = ok
            ? '<span style="color:#22c55e"><i class="bi bi-check-circle me-1"></i>Coinciden</span>'
            : '<span style="color:#ef4444"><i class="bi bi-x-circle me-1"></i>No coinciden</span>';
        confirm.classList.toggle('is-invalid', !ok);
    }

    /* ── Submit de contraseña ───────────────────────── */
    document.getElementById('formPassword')?.addEventListener('submit', function (e) {
        const pass    = document.getElementById('password_nueva');
        const confirm = document.getElementById('password_confirm');
        if (pass && confirm && pass.value !== confirm.value) {
            e.preventDefault();
            document.getElementById('textoConfirm').innerHTML =
                '<span style="color:#ef4444"><i class="bi bi-x-circle me-1"></i>Las contraseñas no coinciden</span>';
            return;
        }
        const btn = document.getElementById('btnCambiarPass');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Guardando...'; }
    });

    /* ── Reset del formulario ───────────────────────── */
    window.resetFormPassword = function () {
        ['textoFortaleza','textoConfirm'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });
        const barra = document.getElementById('barraFortaleza');
        if (barra) barra.style.width = '0%';
        document.getElementById('password_confirm')?.classList.remove('is-invalid');
    };

    /* ── Si hay error de password, hacer scroll a esa sección ── */
    @if($errors->any() && session('seccion') === 'password')
    document.getElementById('seccionPassword')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    @endif

})();
</script>
@endsection
