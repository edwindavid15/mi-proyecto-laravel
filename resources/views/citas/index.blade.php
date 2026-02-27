@extends('layouts.app')

@section('content')
<h1>Mis citas</h1>

<ul>
    @foreach($citas as $c)
        <li>
            {{ $c->fecha }} {{ $c->hora }} - {{ $c->servicio->nombre }} en {{ $c->peluqueria->nombre }} con {{ $c->peluquero->name }}
        </li>
    @endforeach
</ul>

<a href="{{ route('citas.create') }}">Agendar nueva cita</a>
@endsection