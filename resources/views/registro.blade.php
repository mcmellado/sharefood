<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="./media/imagenes/favicon.ico"/>
    <link rel="stylesheet" href="{{ asset('css/registro.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Bienvenido a ShareFood - Registro</title>
</head>
<body>

    <header class="header">
        <nav class="container-sm">  
            <a href="#" class="text-decoration-none text-light me-3">Sobre nosotros</a>
            <a href="#" class="text-decoration-none text-light">Contacto</a>
        </nav>
    </header>

    <div class="container mt-5">
        <h2 class="text-center">¡Regístrate en ShareFood!</h2>
        <p class="lead text-center">Crea tu cuenta y descubre los mejores lugares para disfrutar de deliciosas comidas.</p>

        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <form method="POST" action="{{ route('validar-registro') }}" id="registroForm">
                    @csrf 
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Nombre de usuario:</label>
                        <input type="text" class="form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" value="{{ old('usuario') }}" required pattern="^[A-Za-z0-9_]{3,15}$" title="El nombre de usuario debe tener entre 3 y 15 caracteres y solo puede contener letras, números y guiones bajos (_)">
                        @error('usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Por favor, ingresa un correo electrónico válido.">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" title="La contraseña debe tener al menos una letra y un número, y no puede contener caracteres especiales.">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña:</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" title="La contraseña debe tener al menos una letra y un número, y no puede contener caracteres especiales.">
                    </div>
                    <button type="submit" class="btn btn-dark">Registrarse</button>
                </form>     
                <p class="mt-3">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
                <p class="text-dark">¿Tienes un restaurante? <b><a href="{{ route('registro-restaurante') }}" class="text-dark">Regístrate aquí</a></b></p>
            </div>
        </div>
    </div>

    <footer class="bg-custom p-4 mt-5 fixed-bottom">
        <div class="container-sm text-center text-light">
            <p>&copy; 2023 ShareFood. Todos los derechos reservados.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
