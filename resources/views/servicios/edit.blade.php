<h1>Editar Servicio</h1>

<form action="{{ route('servicios.update', $servicio->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Nombre:</label>
    <input type="text" name="nombre" value="{{ $servicio->nombre }}" required>
    <br><br>

    <label>Descripción:</label>
    <textarea name="descripcion">{{ $servicio->descripcion }}</textarea>
    <br><br>

    <label>Precio:</label>
    <input type="number" name="precio" value="{{ $servicio->precio }}" required>
    <br><br>

    <label>Duración (minutos):</label>
    <input type="number" name="duracion" value="{{ $servicio->duracion }}" required>
    <br><br>

    <button type="submit">Actualizar Servicio</button>
</form>