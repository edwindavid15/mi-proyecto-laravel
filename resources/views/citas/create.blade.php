@extends('layouts.app')

@section('content')
<h1>Agendar cita</h1>

<form action="{{ route('citas.store') }}" method="POST">
    @csrf

    <label>Peluquería:</label>
    <select name="peluqueria_id" required>
        @foreach($peluquerias as $p)
            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
        @endforeach
    </select><br>

    <label>Servicio:</label>
    <select name="servicio_id" required>
        @foreach($servicios as $s)
            <option value="{{ $s->id }}">{{ $s->nombre }} - ${{ $s->precio }}</option>
        @endforeach
    </select><br>

    <label>Peluquero:</label>
    <select name="peluquero_id" required>
        @foreach($peluqueros as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
    </select><br>

    <label>Fecha:</label>
    <input type="date" name="fecha" required><br>

    <label>Hora:</label>
    <input type="time" name="hora" required><br>

    <button type="submit">Agendar cita</button>
</form>
@endsection