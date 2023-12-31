@extends('layouts.app')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/restaurante-perfil.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <div class="container mt-5 scrollable-container">
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
                        <div class="mb-3">
                            <p class="mb-0">Puntuación:</p>
                            <div class="custom-star-rating">
                                @php
                                    $puntuacion = $restaurante->puntuaciones->avg('puntuacion') ?: 0;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $puntuacion)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
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
                        @php
                            $usuarioId = auth()->user()->id;
                            $usuarioBloqueado = DB::table('bloqueados')
                                ->where('usuario_id', $usuarioId)
                                ->where('usuario_bloqueado_id', $comentario->usuario_id)
                                ->exists();
        
                            $usuarioDelComentarioBloqueado = DB::table('bloqueados')
                                ->where('usuario_id', $comentario->usuario_id)
                                ->where('usuario_bloqueado_id', $usuarioId)
                                ->exists();
                        @endphp
        
                        <div class="media mt-3">
                            <div class="media-body">
                                <h5 class="mt-0">
                                    @if(Auth::check() && auth()->user()->id !== $comentario->usuario_id)
                                        @if ($usuarioBloqueado)
                                            No puedes ver este comentario porque el usuario te ha bloqueado.
                                        @elseif ($usuarioDelComentarioBloqueado)
                                            
                                        @else
                                            <a href="{{ route('perfil.ver', ['nombreUsuario' => $comentario->usuario->usuario]) }}" target="_blank">
                                                {{ $comentario->usuario->usuario }}
                                            </a>:
                                        @endif
                                    @else
                                        {{ $comentario->usuario->usuario }}:
                                    @endif
                                </h5>
                                @if (!$usuarioBloqueado && !$usuarioDelComentarioBloqueado)
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
                                @else
                                    No puedes ver este comentario.
                                @endif
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
            
            <!-- Mostrar formulario de puntuación solo si el usuario está autenticado y tiene una reserva -->
            @if ($usuarioReserva)
                @php
                    $fechaReserva = $usuarioReserva->fecha;
                    $haPasadoDeFecha = $usuarioReserva->haPasadoDeFecha();
                @endphp

                <div class="card mt-4">
                    <div class="card-body">
                        @if ($haPasadoDeFecha)
                            @if ($usuarioHaVotado)
                                <p>¡Ya votaste este restaurante, gracias!</p>
                            @else
                                <h3>Puntuar Restaurante</h3>
                                <form action="{{ route('restaurantes.puntuar', ['slug' => $restaurante->slug]) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="puntuacion">Puntuación (1-5):</label>
                                        <select class="form-control" name="puntuacion" id="puntuacion" required>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Puntuar</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

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
