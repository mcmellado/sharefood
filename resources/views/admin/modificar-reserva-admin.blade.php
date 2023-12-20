@extends('layouts.app')

@section('contenido')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/nueva_reserva.css') }}">

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h1 class="mb-4">Modificar Reserva</h1>
                <form action="{{ route('admin.reservas.guardar-modificacion', ['reservaId' => $reserva->id]) }}" method="POST" onsubmit="return validarReserva()">
                    @csrf
                    @method('put') 
                    <div class="form-group">
                        <label for="nueva_fecha">Nueva Fecha</label>
                        <input type="date" name="nueva_fecha" class="form-control" id="nueva_fecha" value="{{ old('nueva_fecha', $reserva->fecha->format('Y-m-d')) }}">
                    </div>
        
                    <div class="form-group">
                        <label for="nueva_hora">Nueva Hora de Reserva:</label>
                        <input type="time" class="form-control" id="nueva_hora" name="nueva_hora" value="{{ $reserva->hora }}" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_personas">Nueva Cantidad de Personas:</label>
                        <input type="number" class="form-control" id="cantidad_personas" name="cantidad_personas" value="{{ $reserva->cantidad_personas }}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="javascript:history.back()" class="btn btn-secondary">Volver Atrás</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        var reservaOriginal = {
            fecha: '{{ $reserva->fecha->format('Y-m-d') }}',
            hora: '{{ $reserva->hora }}',
            cantidad_personas: {{ $reserva->cantidad_personas }},
        };

        function validarReserva() {
            var fechaInput = document.getElementById('nueva_fecha');
            var horaInput = document.getElementById('nueva_hora');
            var cantidadPersonasInput = document.getElementById('cantidad_personas');

            var nuevaFecha = fechaInput.value;
            var nuevaHora = horaInput.value;
            var nuevaCantidadPersonas = parseInt(cantidadPersonasInput.value);

            var fechaSeleccionada = new Date(nuevaFecha + 'T' + nuevaHora);

            // Compara las fechas y horas
            if (
                fechaSeleccionada.getTime() === new Date(reservaOriginal.fecha + 'T' + reservaOriginal.hora).getTime() &&
                nuevaCantidadPersonas === reservaOriginal.cantidad_personas
            ) {
                alert('No has hecho ninguna modificación en la reserva.');
                return false;
            }

            var diaSemana = fechaSeleccionada.toLocaleDateString('es', { weekday: 'long' });

            var horariosRestaurante = {!! json_encode($reserva->restaurante->horarios ?? []) !!};
            var reservasPorFecha = {!! json_encode($reserva->restaurante->reservasPorFecha ?? []) !!};

            var horarioParaDia = horariosRestaurante.find(function (horario) {
                return horario.dia_semana.toLowerCase() === diaSemana.toLowerCase();
            });

            if (!horarioParaDia) {
                alert('El restaurante no está abierto los ' + diaSemana + 's.');
                return false;
            }

            var horaApertura = parseHora(horarioParaDia.hora_apertura);
            var horaCierre = parseHora(horarioParaDia.hora_cierre);
            var horaSeleccionada = parseHora(horaInput.value);

            if (horaSeleccionada < horaApertura || horaSeleccionada > horaCierre) {
                alert('La reserva debe estar dentro del horario de apertura (' + horarioParaDia.hora_apertura + ' - ' + horarioParaDia.hora_cierre + ').');
                return false;
            }

            var fechaActual = new Date();

            if (fechaSeleccionada < fechaActual) {
                alert('La nueva fecha de reserva no puede ser en el pasado.');
                return false;
            }

            var mediaHora = 30 * 60 * 1000; 

            if (horaSeleccionada >= horaCierre - mediaHora) {
                alert('No puede hacer la reserva porque está cerrando o a punto de cerrar. Por favor, elija otro horario.');
                return false;
            }

            var intervaloInicio = new Date(fechaSeleccionada);
            intervaloInicio.setHours(intervaloInicio.getHours() - 1);

            var intervaloFin = new Date(fechaSeleccionada);
            intervaloFin.setHours(intervaloFin.getHours() + 1);

            var reservasEnIntervalo = 0;

            Object.keys(reservasPorFecha).forEach(function (fecha) {
                reservasPorFecha[fecha].forEach(function (reserva) {
                    var fechaReserva = new Date(fecha + 'T' + reserva.hora);
                    if (fechaReserva >= intervaloInicio && fechaReserva <= intervaloFin) {
                        reservasEnIntervalo += parseInt(reserva.personas);
                    }
                });
            });

            reservasEnIntervalo -= parseInt({{ $reserva->cantidad_personas }});

            if (reservasEnIntervalo + parseInt(cantidadPersonasInput.value) > 150) {
                alert('Aforo completo en esos momentos. Por favor, reserva más tarde.');
                return false;
            }

            return true;
        }

        function parseHora(horaString) {
            var partes = horaString.split(':');
            return new Date(1970, 0, 1, partes[0], partes[1]);
        }
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>

@endsection
