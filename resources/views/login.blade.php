<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="./media/imagenes/favicon.ico"/>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Bienvenido a ShareFood</title>
</head>
<body>

    <header class="bg-custom p-3">
        <nav class="container">
            <a href="#" class="text-decoration-none text-light me-3">Sobre nosotros</a>
            <a href="#" class="text-decoration-none text-light">Contacto</a>
        </nav>
    </header>

    <div class="container mt-5">
        <h1 class="display-3 text-center">¡Bienvenido a ShareFood!</h1>
        <p class="lead text-center">Encuentra los mejores lugares para disfrutar de deliciosas comidas cerca de ti.</p>

        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <form method="POST" action="{{route('inicia-sesion') }}">
                    @csrf 
                    <div class="mb-3">
                        <label for="usuario_correo" class="form-label">Usuario o correo electrónico:</label>
                        <input type="text" class="form-control @error('usuario_correo') is-invalid @enderror" id="usuario_correo" name="usuario_correo" value="{{ old('usuario_correo') }}">
                        @error('usuario_correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="contrasenia" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>                    
                    {{-- <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="recuerdame" name="recuerdame">
                        <label class="form-check-label" for="recuerdame">Recuérdame</label>
                            <a href="#" class="ms-2">He olvidado mi contraseña</a> 
                    </div> --}}
                    <button type="submit" class="btn btn-dark">Iniciar sesión</button>
                </form>     
            </div>
            <div class="text-center mt-3">
                <p class="text-dark ">¿Aún no tienes cuenta? <b> <a href="{{ route('registro') }}" class="text-dark">Regístrate aquí</a> </b> </p>
            </div>
        </div>
    </div>

    <footer class="bg-custom p-4 mt-5 fixed-bottom">
        <div class="container text-center text-light">
            <p>&copy; 2023 ShareFood. Todos los derechos reservados.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
