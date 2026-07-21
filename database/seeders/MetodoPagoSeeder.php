<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MetodoPago::firstOrCreate([
            'nombre' => 'QR',
        ], [
            'descripcion' => 'Pago electrónico mediante código QR bancario o billetera móvil.',
        ]);

        MetodoPago::firstOrCreate([
            'nombre' => 'Efectivo',
        ], [
            'descripcion' => 'Pago en efectivo.',
        ]);
    }
}
