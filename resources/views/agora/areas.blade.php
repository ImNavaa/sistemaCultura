@extends('layouts.app')

@section('title', 'Ágora — Áreas')

@section('content')
<div class="container py-4" style="max-width:760px">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('agora.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Áreas del Ágora</h4>
            <p class="small mb-0" style="color:var(--text-muted)">Define las zonas disponibles para reservas parciales</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        {{-- ── Formulario nueva área ── --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-plus-square me-1"></i>Nueva Área</h6>
                    <form action="{{ route('agora.areas.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required placeholder="Ej: Foro Principal">
                            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Descripción</label>
                            <textarea name="descripcion" class="form-control form-control-sm" rows="2"
                                      placeholder="Descripción breve (opcional)">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Color</label>
                                <input type="color" name="color" class="form-control form-control-sm form-control-color"
                                       value="{{ old('color', '#6366f1') }}" style="height:36px">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Capacidad</label>
                                <input type="number" name="capacidad" class="form-control form-control-sm"
                                       value="{{ old('capacidad') }}" min="1" placeholder="Personas">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Orden de visualización</label>
                            <input type="number" name="orden" class="form-control form-control-sm"
                                   value="{{ old('orden', 0) }}" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i>Agregar Área
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Lista de áreas ── --}}
        <div class="col-md-7">
            @if($areas->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5" style="color:var(--text-muted)">
                    <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-30"></i>
                    <p class="mb-0">Aún no hay áreas configuradas.</p>
                    <p class="small">Agrega las zonas del Ágora desde el formulario.</p>
                </div>
            </div>
            @else
            <div class="d-flex flex-column gap-2">
                @foreach($areas as $area)
                <div class="card border-0 shadow-sm" style="border-left:4px solid {{ $area->color }} !important">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold">{{ $area->nombre }}</span>
                                    @if(!$area->activa)
                                    <span class="badge bg-secondary" style="font-size:.65rem">Inactiva</span>
                                    @endif
                                    @if($area->capacidad)
                                    <span class="badge" style="font-size:.65rem;background:{{ $area->color }}22;color:{{ $area->color }};border:1px solid {{ $area->color }}44">
                                        cap. {{ $area->capacidad }}
                                    </span>
                                    @endif
                                </div>
                                @if($area->descripcion)
                                <div class="small text-muted">{{ $area->descripcion }}</div>
                                @endif
                            </div>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <button class="btn btn-sm btn-link p-1 text-muted"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarArea"
                                        data-area="{{ json_encode($area) }}"
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('agora.areas.destroy', $area) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar el área {{ $area->nombre }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link p-1 text-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal editar área --}}
<div class="modal fade" id="modalEditarArea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditarArea" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Editar Área</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="eaNombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea name="descripcion" id="eaDescripcion" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Color</label>
                            <input type="color" name="color" id="eaColor" class="form-control form-control-sm form-control-color" style="height:36px">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Capacidad</label>
                            <input type="number" name="capacidad" id="eaCapacidad" class="form-control form-control-sm" min="1">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Orden</label>
                            <input type="number" name="orden" id="eaOrden" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-6 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activa" id="eaActiva" value="1">
                                <label class="form-check-label small" for="eaActiva">Activa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-bs-target="#modalEditarArea"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var a = JSON.parse(this.dataset.area);
        document.getElementById('eaNombre').value      = a.nombre       ?? '';
        document.getElementById('eaDescripcion').value = a.descripcion  ?? '';
        document.getElementById('eaColor').value       = a.color        ?? '#6366f1';
        document.getElementById('eaCapacidad').value   = a.capacidad    ?? '';
        document.getElementById('eaOrden').value       = a.orden        ?? 0;
        document.getElementById('eaActiva').checked    = !!a.activa;
        document.getElementById('formEditarArea').action = '/agora/areas/' + a.id;
    });
});
</script>
@endsection
