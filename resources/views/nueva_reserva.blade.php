@extends('layouts.app')

@section('contenido')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-JA9LSTp+ZMfsB01d5WVTTK9K4xXvZF7S81Lp6FDtkFZFM4/+r2kZU5JlQa86j6A+xEBk2OL/xCUZQpG6RbApRg==" crossorigin="anonymous" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha512-Gn5384xq1ii1+FXMYFcUfuBWCAtb2JaeQGfcYxpPuwvc8vR+5tZ/sM47KaS5tn9eqODJdSqGXCXT9RQZBmqQK/eg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-9BScY9K9B8WV0Ec4zLDI2+rBXTXD4bU+TfZK7K0aGj4O7TC2M0Zg9urN42Yq4oYH1C8chY3Leb4Ju6Xvmf9CEw==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="{{ asset('css/nueva_reserva.css') }}">

<div class="container mt-5">
    <div id="alerts-container"></div>
    <div class="card">
        <div class="card-body">
            <h1 class="mb-4">Hacer Reserva</h1>
            <form action="{{ route('restaurantes.guardarReserva', ['slug' => $restaurante->slug]) }}" method="POST" onsubmit="return validarReserva()">
                @csrf
                <div class="form-group">
                    <label for="fecha">Fecha de la Reserva:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" onchange="cargarHorasDisponibles()" required>
                </div>
                <div class="form-group">
                    <label for="hora">Hora de la Reserva:</label>
                    <select class="form-control" id="hora" name="hora" required>
                        <option value="" disabled selected>Seleccionar primero la fecha</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad_personas">Cantidad de personas:</label>
                    <input type="number" class="form-control" id="cantidad_personas" name="cantidad_personas" required>
                </div>
                <button type="submit" class="btn btn-success">Confirmar Reserva</button>
                <a href="{{ route('restaurantes.perfil', ['slug' => $restaurante->slug ]) }}" class="btn btn-secondary">Volver al Perfil</a>
            </form>
        </div>
    </div>
</div>

<script>
    function formatHora(hora) {
        var horas = hora.getHours().toString().padStart(2, '0');
        var minutos = hora.getMinutes().toString().padStart(2, '0');
        return horas + ':' + minutos;
    }

    function obtenerHorasReservadas(fechaSeleccionada) {
        var reservasParaFecha = reservasPorFecha[fechaSeleccionada] || [];
        return reservasParaFecha.map(function (reserva) {
            return reserva.hora;
        });
    }

    function cargarHorasDisponibles() {
        var fechaSeleccionada = document.getElementById('fecha').value;

        if (!fechaSeleccionada) {
            document.getElementById('hora').disabled = true;
            return;
        }

        function normalizarDia(dia) {
            return dia.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        var reservasParaFecha = obtenerHorasReservadas(fechaSeleccionada);
        var diaSemana = normalizarDia(new Date(fechaSeleccionada).toLocaleDateString('es', { weekday: 'long' }));
        var horarioParaDia = horariosRestaurante.find(function (horario) {
            return horario.dia_semana.toLowerCase() === diaSemana.toLowerCase();
        });
        var horasDisponibles = obtenerHorasDisponibles(horarioParaDia.hora_apertura, horarioParaDia.hora_cierre, reservasParaFecha);
        var selectHora = document.getElementById('hora');
        
        selectHora.disabled = false;
        selectHora.innerHTML = '';

        horasDisponibles.forEach(function (hora) {
            var option = document.createElement('option');
            option.value = hora;
            option.text = hora;
            selectHora.appendChild(option);
        });
    }

    function obtenerHorasDisponibles(horaApertura, horaCierre, reservas) {
    var horasDisponibles = [];
    var tiempoCierre = "{{ $restaurante->tiempo_cierre }}";

    if (horaApertura === horaCierre || (horaApertura === '00:00:00' && horaCierre === '00:00:00')) {
        for (var hora = 0; hora < 24; hora++) {
            for (var minuto = 0; minuto < 60; minuto += 30) {
                var horaActualString = hora.toString().padStart(2, '0') + ':' + minuto.toString().padStart(2, '0');
                horasDisponibles.push(horaActualString);
            }
        }
    } else {
        var horaActual = parseHora(horaApertura);
        var horaCierreModificada = parseHora(horaCierre).setMinutes(parseHora(horaCierre).getMinutes() - tiempoCierre);

        while (horaActual <= horaCierreModificada) {
            var horaActualString = formatHora(horaActual);
            if (!reservas.includes(horaActualString)) {
                horasDisponibles.push(horaActualString);
            }
            horaActual.setMinutes(horaActual.getMinutes() + 30);
        }
    }

        return horasDisponibles;
    }


    function mostrarAlerta(mensaje, tipo) {
        var alertsContainer = document.getElementById('alerts-container');

        var alertElement = document.createElement('div');
        alertElement.className = 'alert alert-' + tipo + ' alert-dismissible fade show';
        alertElement.role = 'alert';
        alertElement.innerHTML = `
            ${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;

        alertsContainer.appendChild(alertElement);

        setTimeout(function() {
            alertElement.remove();
        }, 20000);
    }

    var reservasPorFecha = {};

    @foreach($restaurante->reservas as $reserva)
        var fecha = "{{ $reserva->fecha }}";
        if (!reservasPorFecha[fecha]) {
            reservasPorFecha[fecha] = [];
        }
        reservasPorFecha[fecha].push({
            hora: "{{ $reserva->hora }}",
            personas: "{{ $reserva->cantidad_personas }}"
        });
    @endforeach

    var horariosRestaurante = {!! json_encode($restaurante->horarios) !!};

    function validarReserva() {
        var fechaInput = document.getElementById('fecha');
        var horaInput = document.getElementById('hora');
        var cantidadPersonasInput = document.getElementById('cantidad_personas');

        var fechaActual = new Date();
        var fechaSeleccionada = new Date(fechaInput.value + 'T' + horaInput.value);

        if (fechaSeleccionada < fechaActual) {
            mostrarAlerta('La fecha de la reserva no puede ser anterior a la fecha actual.', 'danger');
            return false;
        }

        function normalizarDia(dia) {
            return dia.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        var diaSemana = normalizarDia(new Date(fechaSeleccionada).toLocaleDateString('es', { weekday: 'long' }));
        var horarioParaDia = horariosRestaurante.find(function (horario) {
            return horario.dia_semana.toLowerCase() === diaSemana.toLowerCase();
        });

        if (!horarioParaDia) {
            mostrarAlerta('El restaurante no está abierto los ' + diaSemana + 's.', 'danger');
            return false;
        }

        var horaApertura = parseHora(horarioParaDia.hora_apertura);
        var horaCierre = parseHora(horarioParaDia.hora_cierre);
        var horaSeleccionada = parseHora(horaInput.value);

        if (horaApertura.getTime() === horaCierre.getTime()) {
            // Abierto un 24 horas equisde
            return true;
        }


        if (horaSeleccionada < horaApertura || horaSeleccionada > horaCierre) {
            mostrarAlerta('La reserva debe estar dentro del horario de apertura (' + horarioParaDia.hora_apertura + ' - ' + horarioParaDia.hora_cierre + ').', 'danger');
            return false;
        }

        var reservasEnIntervalo = 1;
        var tiempoPermanencia = parseInt("{{ $restaurante->tiempo_permanencia }}") * 60 * 1000;
        var minuto = 1 * 60 * 1000;
        tiempoPermanencia = tiempoPermanencia - minuto;


        var intervaloInicio = new Date(fechaSeleccionada - tiempoPermanencia);
        var intervaloFin = new Date(fechaSeleccionada + tiempoPermanencia);

        Object.keys(reservasPorFecha).forEach(function (fecha) {
            reservasPorFecha[fecha].forEach(function (reserva) {
                var fechaReserva = new Date(fecha + 'T' + reserva.hora);
                if (fechaReserva >= intervaloInicio && fechaReserva <= intervaloFin) {
                    reservasEnIntervalo += parseInt(reserva.personas);
                }
            });
        });

        if (reservasEnIntervalo + parseInt(cantidadPersonasInput.value) > {{ $restaurante->aforo_maximo }} + 1) {
            mostrarAlerta('Aforo completo en esos momentos. Por favor, reserva más tarde.', 'danger');
            return false;
        }

        return true;
    }

    function parseHora(horaString) {
        var partes = horaString.split(':');
        return new Date(1970, 0, 1, partes[0], partes[1]);
    }
</script>

@endsection