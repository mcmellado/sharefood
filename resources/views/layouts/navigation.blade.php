<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">
        <span style="font-size: 1.5em;">Sharefood</span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('index') }}">Inicio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('restaurantes') }}">Ver Restaurantes</a>
        </li>
        <li class="nav-item">
            @auth
                <a class="nav-link" href="{{ route('perfil') }}">{{ Auth::user()->name }}</a>
            @else
                <a class="nav-link" href="{{ route('perfil') }}">Log in</a>
            @endauth
        </li>
    </ul>
</nav>
