@extends('layouts.app')

@section('title', 'Nuevo Proyecto')

@section('content')
<div class="container py-4" style="max-width:720px">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('proyectos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-plus-square me-2"></i>Nuevo Proyecto</h4>
    </div>

    @if($errors->any())
    <div class="alert alert-danger py-2">
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('proyectos.store') }}" method="POST">
                @csrf
                @include('proyectos._form', ['proyecto' => null, 'miembrosIds' => []])
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Crear Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
