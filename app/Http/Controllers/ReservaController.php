<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Model;

class ReservaController extends Controller
{
    public function nuevaReserva($slug)
    {
        
        $restaurante = Restaurante::where('slug', $slug)->firstOrFail();

        return view('nueva_reserva', ['restaurante' => $restaurante]);
        
    }
    
    public function guardarReserva(Request $request, $slug)
    {
        $restaurante = Restaurante::where('slug', $slug)->firstOrFail();
        $restauranteId = $restaurante->id;

        Reserva::create([
            'usuario_id' => auth()->id(),
            'restaurante_id' => $restauranteId,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'cantidad_personas' => $request->cantidad_personas
        ]);
        
        session()->flash('reserva-confirmada', 'Tu reserva ha sido confirmada. ¡Gracias por elegir nuestro restaurante!');

        
        return redirect()->route('restaurantes.perfil', ['slug' => $slug]);
    }

    public function confirmarReserva($slug)
    {
        return view('reservas.confirmar_reserva', ['slug' => $slug]);
    }

    public function cancelar(Reserva $reserva)
    {

        $reserva->delete(); 

        return redirect()->back()->with('success', 'Reserva cancelada correctamente.');
    }
}