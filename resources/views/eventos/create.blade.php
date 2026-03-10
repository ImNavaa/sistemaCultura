<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Crear Evento</h2>

    @if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre') }}">
        <br><br>

        <label>Fecha:</label>
        <input type="date" name="fecha" value="{{ old('fecha') }}">
        <br><br>

        <label>Descripción:</label>
        <textarea name="descripcion">{{ old('descripcion') }}</textarea>
        <br><br>

        <label>Organiza:</label>
        <select name="organiza_id">
            <option value="">Seleccione</option>
            @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}"
                {{ old('organiza_id') == $usuario->id ? 'selected' : '' }}>
                {{ $usuario->name }}
            </option>
            @endforeach
        </select>
        <br><br>

        <label>Agenda:</label>
        <select name="agenda_id">
            <option value="">Seleccione</option>
            @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}"
                {{ old('agenda_id') == $usuario->id ? 'selected' : '' }}>
                {{ $usuario->name }}
            </option>
            @endforeach
        </select>
        <br><br>

        <button type="submit">Guardar</button>
    </form>


</body>
</html>