@extends('layouts.app')

@section('content')
<h1>Agregar Servicio</h1>

<form action="{{ route('servicios.store') }}" method="POST">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br>

    <label>Descripción:</label>
    <textarea name="descripcion"></textarea><br>

    <input type="number" name="duracion" placeholder="Duración en minutos">

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" required><br>

    <button type="submit">Guardar</button>
</form>
@endsection