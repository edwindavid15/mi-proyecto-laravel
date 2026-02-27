@extends('layouts.app')

@section('content')
<h1>Servicios disponibles</h1>

<ul>
    @foreach($servicios as $s)
        <li>{{ $s->nombre }} - ${{ $s->precio }}</li>
    @endforeach
</ul>

<a href="{{ route('servicios.create') }}">Agregar nuevo servicio</a>
@endsection