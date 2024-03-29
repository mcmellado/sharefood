<!-- resources/views/panel_admin_usuarios.blade.php -->
@extends('layouts.app')

@section('contenido')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/perfil_admin.css') }}">
    <!-- Añade SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <div class="container">
        <h2 class="mt-4 mb-4">Panel de administrador de usuarios:</h2>

        <div class="row">
            <div class="col-md-12">

                @if(session('contrasena-cambiada'))
                    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                        {{ session('contrasena-cambiada') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('usuario-eliminado'))
                    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                        {{ session('usuario-eliminado') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('usuario-modificado'))
                    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                        {{ session('usuario-modificado') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Teléfono</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->usuario }}</td>
                                    <td class="text-center">{{ $user->email }}</td>
                                    <td class="text-center">{{ $user->telefono ?? 'No tiene' }}</td>
                                    <td class="text-center">
                                        <form method="post" action="{{ route('admin.validar', $user->id) }}" style="display:inline">
                                            @csrf
                                            @if ($user->validacion)
                                                <button type="submit" class="btn btn-success btn-sm" title="Validado"><i class="fa fa-check"></i></button>
                                            @else
                                                <button type="submit" class="btn btn-danger btn-sm" title="Invalidar"><i class="fa fa-times"></i></button>
                                            @endif
                                        </form>
                                        
                                        <form method="post" action="{{ route('admin.eliminar', $user->id) }}" class="eliminar-usuario-form" style="display:inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"><i class="fa fa-trash"></i></button>
                                        </form>
                                        
                                        <a href="{{ route('admin.usuarios.modificar', $user->id) }}" class="btn btn-primary btn-sm" title="Modificar"><i class="fa fa-pencil-alt"></i></a>
                                        <a href="{{ route('admin.usuarios.cambiar-contrasena-admin', $user->id) }}" class="btn btn-warning btn-sm" title="Cambiar Contraseña"><i class="fa fa-key"></i></a>
                                        <a href="{{ route('admin.usuarios.ver-comentarios', $user->id) }}" class="btn btn-info btn-sm" title="Ver Comentarios"><i class="fa fa-comment"></i></a>
                                        <a href="{{ route('admin.usuarios.ver-reservas', $user->id) }}" class="btn btn-info btn-sm" title="Ver Reservas"><i class="fa fa-calendar"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <a href="{{ route('admin.panel-admin-restaurante') }}" class="btn btn-primary independent-btn">Ir al Panel de Restaurantes</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('js/panel_admin_usuarios.js') }}"></script>

@endsection
