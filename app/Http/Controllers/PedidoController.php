<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Restaurante;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoCancelado;
use Illuminate\Support\Facades\Auth;





class PedidoController extends Controller
{
    public function realizarPedido(Request $request)
    {
        $restauranteId = $request->input('restaurante_id');

        if (!$this->restauranteEstaAbierto($restauranteId)) {
        return redirect()->back()->with('error_message', 'El restaurante está cerrado en este momento.');
        }

        $platos = [];
        $restauranteId = $request->input('restaurante_id');
        $productos = $request->input('productos');
        $direccion = $request->input('direccion');
        $precioTotalPedido = 0;

        if (empty($productos)) {
            return redirect()->back()->with('error_message', 'No has marcado ningún producto.');
        }

        $pedido = new Pedido();
        $pedido->usuario_id = auth()->id();
        $pedido->restaurante_id = $restauranteId;
        $pedido->estado = 'pendiente';
        $pedido->direccion = $direccion;

        foreach ($productos as $productoId => $cantidad) {
            $producto = Producto::find($productoId);

            if ($producto && $producto->restaurante_id == $restauranteId) {
                $precioTotalProducto = $producto->precio * $cantidad;
                $precioTotalPedido += $precioTotalProducto;

                $platos[] = [
                    'id' => $productoId,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'cantidad' => $cantidad,
                ];
        }
    }

        $pedido->platos = json_encode($platos);

        $pedido->precio_total = $precioTotalPedido;
        $pedido->save();

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $lineItems = [];

        foreach ($platos as $plato) {
            $cantidad = (int) $plato['cantidad'];
            if ($cantidad >= 1) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $plato['nombre'],
                            'description' => $plato['descripcion'],
                        ],
                        'unit_amount' => $plato['precio'] * 100,
                    ],
                    'quantity' => $cantidad,
                ];
            } else {

            }
        }

        if (empty($lineItems)) {
            // no se que poner

        } else {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['restauranteId' => $restauranteId]),
                'cancel_url' => route('stripe.cancel', ['restauranteId' => $restauranteId]),
            ]);
        }

        return redirect()->away($session->url);
    }

    public function stripeSuccess(Request $request)
{
    $restauranteId = $request->input('restauranteId');
    $pedido = Pedido::where('restaurante_id', $restauranteId)
        ->where('usuario_id', auth()->id())
        ->latest()
        ->first();

    if ($pedido) {
        $pedido->estado = 'pagado';
        $pedido->save();
    }

    return redirect()->route('restaurante.mostrar_carta', ['id' => $restauranteId])->with('success_message', '¡Pedido realizado con éxito, tu pedido llegará pronto a casa!');
}

public function stripeCancel(Request $request)
{
    $restauranteId = $request->input('restauranteId');
    $pedido = Pedido::where('restaurante_id', $restauranteId)
        ->where('usuario_id', auth()->id())
        ->latest()
        ->first();

    return redirect()->route('restaurante.mostrar_carta', ['id' => $restauranteId])->with('error_message', '¡El pago ha sido cancelado!');
}

public function getRestauranteSlug($restauranteId)
{
    $restaurante = Restaurante::find($restauranteId);

    if ($restaurante) {
        return $restaurante->slug;
    }

    return null;
}

public function verPedidos($slug)
{
    $restaurante = Restaurante::where('slug', $slug)->first();

    if (!$restaurante) {
        abort(404);
    }

    $pedidos = Pedido::where('restaurante_id', $restaurante->id)->get();

    return view('ver_pedidos', compact('restaurante', 'pedidos'));
}

public function restauranteEstaAbierto($restauranteId)
{
    Carbon::setLocale('es');

    $horaActual = Carbon::now()->addHour();
    $diaSemanaActual = $horaActual->format('l');
    $mapeoDias = [
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miercoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sabado',
        'Sunday' => 'domingo',
    ];

    $diaSemanaActualEnEspanol = $mapeoDias[$diaSemanaActual];

    $horariosRestaurante = Horario::where('restaurante_id', $restauranteId)
        ->where('dia_semana', $diaSemanaActualEnEspanol)
        ->get();

    foreach ($horariosRestaurante as $horario) {
        $horaApertura = Carbon::parse($horario->hora_apertura);
        $horaCierre = Carbon::parse($horario->hora_cierre);

        $fechaActual = $horaActual->toDateString();
        $horaApertura->setDateFrom($fechaActual);
        $horaCierre->setDateFrom($fechaActual);

        if ($horaCierre->format('H:i:s') === '00:00:00') {
            $horaCierre->setTime(23, 59, 59);
        }

        // dd([
        //     'horaActual' => $horaActual->format('Y-m-d H:i:s'),
        //     'horaApertura' => $horaApertura->format('Y-m-d H:i:s'),
        //     'horaCierre' => $horaCierre->format('Y-m-d H:i:s'),
        // ]);

        if ($horaActual >= $horaApertura && $horaActual <= $horaCierre) {
            return true;
        }

        if ($horaApertura == $horaCierre) {
            return true;
        }
    }

    return false;
}


public function cancelarPedido(Request $request, Pedido $pedido)
{
    $justificacion = $request->input('justificacion', 'Sin justificación');

    Mail::to($pedido->usuario->email)->send(new PedidoCancelado($pedido, $justificacion));
    $pedido->delete();

    return redirect()->back()->with('success', 'Pedido cancelado correctamente');
}

public function cancelarPedidoUsuario($pedidoId)
{
    try {
        $pedido = Pedido::findOrFail($pedidoId);

        if (Auth::user()->id == $pedido->usuario_id) {
            $pedido->update(['estado' => 'cancelado']);

            return response()->json(['message' => 'Pedido cancelado correctamente'], 200);
        } else {
            return response()->json(['error' => 'No tienes permisos para cancelar este pedido'], 403);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al cancelar el pedido'], 500);
    }
}

}