<h1>{{ $servicio->nombre }}</h1>

<p><strong>Descripción:</strong> {{ $servicio->descripcion }}</p>
<p><strong>Precio:</strong> ${{ $servicio->precio }}</p>
<p><strong>Duración:</strong> {{ $servicio->duracion }} minutos</p>

<a href="{{ route('servicios.edit', $servicio->id) }}">
    Editar
</a>