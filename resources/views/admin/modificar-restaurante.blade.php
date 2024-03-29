@extends('layouts.app')

<title> Modificar restaurante </title>


@section('contenido')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/modificar-restaurante-usuario.css') }}">


<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h1 class="mb-4">Modificar Restaurante</h1>

            <form method="post" action="{{ route('admin.restaurantes.actualizar', $restaurante->id) }}" enctype="multipart/form-data" onsubmit="return validarRestaurante()">   
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $restaurante->nombre) }}" class="form-control" required>
                    @error('nombre')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $restaurante->direccion) }}" class="form-control" required>
                    @error('direccion')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sitio_web">Sitio web:</label>
                    <input type="text" name="sitio_web" value="{{ old('sitio_web', $restaurante->sitio_web) }}" class="form-control">
                    @error('sitio_web')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" pattern="\d{3}-\d{3}-\d{4}" title="Formato de teléfono: xxx-xxx-xxxx" value="{{ old('telefono', $restaurante->telefono) }}" class="form-control" required>
                    @error('telefono')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gastronomia">Gastronomía:</label>
                    <input type="text" name="gastronomia" value="{{ old('gastronomia', $restaurante->gastronomia) }}" class="form-control">
                    @error('gastronomia')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="aforo_maximo">Aforo Máximo:</label>
                    @if(!$restaurante->tieneReservasFuturas())
                        <input type="number" name="aforo_maximo" value="{{ old('aforo_maximo', $restaurante->aforo_maximo) }}" class="form-control" required>
                        @error('aforo_maximo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @else
                        <p class="text-danger">No puedes modificar el aforo máximo con reservas próximas pendientes.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="tiempo_permanencia">Tiempo de Permanencia (en minutos):</label>
                    @if(!$restaurante->tieneReservasFuturas())
                        <input type="number" name="tiempo_permanencia" value="{{ old('tiempo_permanencia', $restaurante->tiempo_permanencia) }}" class="form-control" required>
                        @error('tiempo_permanencia')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @else
                        <p class="text-danger">No puedes modificar el tiempo de permanencia con reservas próximas pendientes.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="tiempo_cierre">Tiempo de cierre (en minutos):</label>
                    @if(!$restaurante->tieneReservasFuturas())
                        <input type="number" name="tiempo_cierre" value="{{ old('tiempo_cierre', $restaurante->tiempo_cierre) }}" class="form-control">
                        @error('tiempo_cierre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @else
                        <p class="text-danger">No puedes modificar el tiempo de cierre con reservas próximas pendientes.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" class="form-control-file" id="imagen" name="imagen">
                </div>
                
                <a href="{{ route('admin.panel-admin-restaurante') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i></a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/modificar_restaurante_admin.js') }}" defer></script>
@endsection

    