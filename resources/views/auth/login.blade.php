<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Sistema Cultura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        .login-body { padding: 2rem; }
        .btn-login {
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            border: none;
            color: white;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
        }
        .btn-login:hover { opacity: 0.9; color: white; }
        .form-control:focus {
            border-color: #0f3460;
            box-shadow: 0 0 0 0.2rem rgba(15,52,96,0.25);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-building"></i>
        <h4 class="mb-0 fw-bold">Sistema Cultura</h4>
        <small class="opacity-75">Teatro Municipal</small>
    </div>

    <div class="login-body">
        <h5 class="mb-4 text-center text-muted">Iniciar Sesión</h5>

        @if($errors->any())
        <div class="alert alert-danger py-2">
            <i class="bi bi-exclamation-triangle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('status'))
        <div class="alert alert-success py-2">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="formLogin">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-envelope"></i> Correo electrónico
                </label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="correo@ejemplo.com"
                       autofocus required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-lock"></i> Contraseña
                </label>
                <div class="input-group">
                    <input type="password" name="password" id="campoPassword"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Tu contraseña" required>
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePassword()">
                        <i class="bi bi-eye" id="iconoPassword"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input"
                           id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="remember">
                        Recordarme
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100" id="btnLogin">
                <i class="bi bi-box-arrow-in-right"></i> Entrar al sistema
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const campo = document.getElementById('campoPassword');
    const icono = document.getElementById('iconoPassword');
    if (campo.type === 'password') {
        campo.type = 'text';
        icono.className = 'bi bi-eye-slash';
    } else {
        campo.type = 'password';
        icono.className = 'bi bi-eye';
    }
}

document.getElementById('formLogin').addEventListener('submit', function() {
    const btn = document.getElementById('btnLogin');
    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Entrando...';
});
</script>

</body>
</html>