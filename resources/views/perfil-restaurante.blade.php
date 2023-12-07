@extends('layouts.app')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/restaurante-perfil.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

<div class="container mt-5 scrollable-container">
    {{-- Alerta de Reserva Confirmada --}}
    @if(session('reserva-confirmada'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('reserva-confirmada') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            $(document).ready(function(){
                $('#confirmacionReserva').modal('show');
            });
        </script>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h1>{{ $restaurante->nombre }}</h1>
                    <p class="text-muted">{{ $restaurante->gastronomia }}</p>
                    <p>{{ $restaurante->direccion }}</p>
                    <p>Teléfono: {{ $restaurante->telefono }}</p>
                    <p>Sitio web: <a href="{{ $restaurante->sitio_web }}" target="_blank">{{ $restaurante->sitio_web }}</a></p>
                    <p>Puntuación: {{ $restaurante->puntuacion }} &#9733;</p>
                </div>
                <div class="col-md-4">
                    {{-- <img src="{{ asset($restaurante->imagen) }}" alt="{{ $restaurante->nombre }}" class="img-fluid"> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Mostrar horarios si existen --}}
    @if ($restaurante->horarios)
        <div class="card mt-4">
            <div class="card-body">
                <h3>Horarios:</h3>
                @foreach ($restaurante->horarios as $horario)
                    <p>{{ $horario->dia_semana }}: {{ \Carbon\Carbon::parse($horario->hora_apertura)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <h3>Comentarios:</h3>
            <div id="comentarios-existentes" class="scrollable-content">
                @forelse ($restaurante->comentarios as $comentario)
                    <div class="media mt-3">
                        <div class="media-body">
                            <h5 class="mt-0">{{ $comentario->usuario->usuario }}:</h5>
                            {{ $comentario->contenido }}
                            @auth
                                @if(auth()->user()->id == $comentario->usuario_id)
                                    {{-- Formulario para eliminar comentario --}}
                                    <form action="{{ route('restaurantes.eliminarComentario', ['comentarioId' => $comentario->id]) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar Comentario</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <p>No hay comentarios aún.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Agregar nuevo comentario --}}
    @auth
        <div id="agregar-comentario" class="card mt-4">
            <div class="card-body">
                <form action="{{ route('restaurantes.comentar', ['restauranteId' => $restaurante->slug]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="contenido">Agregar Comentario:</label>
                        <textarea class="form-control" id="contenido" name="contenido" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Comentar</button>
                </form>
            </div>
        </div>

        {{-- Botón para hacer reserva solo si el usuario está autenticado --}}
        <div id="hacer-reserva" class="card mt-4">
            <div class="card-body">
                <a href="{{ route('restaurantes.nuevaReserva', ['slug' => $restaurante->slug]) }}" class="btn btn-success">Hacer Reserva</a>
            </div>
        </div>
    @else
        <p class="mt-4">Inicia sesión para dejar un comentario o realizar una reserva.</p>
    @endauth
</div>
@endsection
