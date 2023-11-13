<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantesTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('restaurantes')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('restaurantes')->insert([
            [
                'nombre' => 'La Parrilla del Valle',
                'direccion' => 'Av. Principal 123',
                'sitio_web' => 'https://www.parrilladelvalle.com',
                'telefono' => '+123456789',
            ],
            [
                'nombre' => 'Sabores del Mar',
                'direccion' => 'Calle 5, Zona Costera',
                'sitio_web' => 'https://www.saboresdelmar.com',
                'telefono' => '+987654321',
            ],
            [
                'nombre' => 'Pizzería Bella Italia',
                'direccion' => 'Via Roma 789',
                'sitio_web' => 'https://www.bellaitalia.com',
                'telefono' => '+112233445',
            ],
            [
                'nombre' => 'Comida Mexicana Tradicional',
                'direccion' => 'Av. Hidalgo 456',
                'sitio_web' => 'https://www.mexicanatradicional.com',
                'telefono' => '+554433221',
            ],
            [
                'nombre' => 'Sushi Express',
                'direccion' => 'Calle Sushiman 101',
                'sitio_web' => 'https://www.sushiexpress.com',
                'telefono' => '+998877665',
            ],
        ]);
    }
}