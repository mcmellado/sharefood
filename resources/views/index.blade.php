@extends('layouts.app')

@section('contenido')

<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">



<div class="container mt-5">
    <form action="{{ route('restaurantes.buscar') }}" method="GET">
        <div class="input-group mb-3 position-relative"> 
            <input type="text" class="form-control" placeholder="Buscar restaurantes..." name="q" id="buscar-input">
            <div class="sugerencias-desplegable position-absolute" id="sugerencias-desplegable"></div>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </div>
        </div>
    </form>
</div>

<div class="espaciado-superior">
    <div class="row mt-1">
        <!-- Div 1 -->
        <div class="col-md-4">
            <div class="cajita bg-verde text-white">
                <i class="material-icons mb-3 icono-verde">restaurant</i>
                <h4 class="mb-4 display-6">Encuentra los mejores lugares para comer</h4>
                <p class="lead">4.3 millones de restaurantes, desde puestos de comida en la calle a establecimientos de lujo.</p>
            </div>
        </div>
        <!-- Div 2 -->
        <div class="col-md-4">
            <div class="cajita bg-verde text-white">
                <i class="material-icons mb-3 icono-verde">comment</i>
                <h4 class="mb-4 display-6">Descubre las opiniones más recientes</h4>
                <p class="lead">Millones de opiniones sobre restaurantes y fotos de nuestra comunidad global de comensales.</p>
            </div>  
        </div>
        <!-- Div 3 -->
        <div class="col-md-4">
            <div class="cajita bg-verde text-white">
                <i class="material-icons mb-3 icono-verde">event</i>
                <h4 class="mb-4 display-6">Reserva online en restaurantes de todo el mundo</h4>
                <p class="lead">Haz tus reservas fácilmente a través de nuestra plataforma en línea.</p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <h2 class="titulo text-verde display-4">Descubre los Mejores Restaurantes: </h2>
        <div class="custom-row-item my-4">
            @php
                    $mejoresLocales = \App\Models\Restaurante::with('puntuaciones')
                        ->take(4)
                        ->get();

                    $mejoresLocales = $mejoresLocales->sortBy('puntuacion');
            @endphp

        

<div class="row">
    @forelse($mejoresLocales as $local)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-verde">
                <img src="{{ $local->imagen }}" class="card-img-top img-fluid img-thumbnail mx-auto" alt="{{ $local->nombre }}" style="height: 300px; object-fit: cover;background-color: #343a40!important;">
                <div class="card-body">
                    <h5 class="card-title text-truncate">
                        <a href="{{ route('restaurantes.perfil', $local->slug) }}" class="card-title">
                            {{ $local->nombre }}
                        </a>
                    </h5>
                    <p class="card-text text-muted">{{ $local->gastronomia }}</p>
                </div>
                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                    <p class="mb-0">Puntuación:</p>
                    <div class="custom-star-rating">
                        @php
                            $puntuacion = $local->puntuaciones->avg('puntuacion') ?: 0;
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
        </div>
    @empty
        <p class="col-12 text-center text-verde">No hay locales disponibles.</p>
    @endforelse
</div>
<br>




<script>
    var desplegable = document.getElementById('sugerencias-desplegable');
    var inputBuscar = document.getElementById('buscar-input');

    inputBuscar.addEventListener('input', function () {
        var query = this.value;

        if (query.length >= 3) {
            fetch(`/restaurantes/buscar-sugerencias?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    actualizarDesplegableSugerencias(data);
                })
                .catch(error => {
                    console.error('Error al obtener sugerencias:', error);
                });
        } else {
            desplegable.innerHTML = ''; 
        }
    });

    function actualizarDesplegableSugerencias(sugerencias) {
        desplegable.innerHTML = '';

        if (sugerencias.length === 0) {
            desplegable.style.display = 'none';
            return;
        }

        sugerencias.forEach(function (sugerencia) {
            var sugerenciaItem = document.createElement('div');
            sugerenciaItem.className = 'sugerencia-item';

            // Crear un enlace al perfil del restaurante
            var enlacePerfil = document.createElement('a');
            enlacePerfil.href = `/restaurantes/${sugerencia.slug}`;
            enlacePerfil.textContent = sugerencia.nombre;

            sugerenciaItem.appendChild(enlacePerfil);
            desplegable.appendChild(sugerenciaItem);
        });

        desplegable.style.display = 'block';
    }

    document.addEventListener('click', function (event) {
        if (!desplegable.contains(event.target)) {
            desplegable.style.display = 'none';
        }
    });
</script>

@endsection 