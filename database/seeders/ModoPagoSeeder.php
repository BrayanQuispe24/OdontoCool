<?php

namespace Database\Seeders;

use App\Models\ModoPago;
use Illuminate\Database\Seeder;

class ModoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModoPago::firstOrCreate([
            'nombre' => 'CONTADO',
        ], [
            'estado' => 'ACTIVO',
        ]);

        ModoPago::firstOrCreate([
            'nombre' => 'CREDITO',
        ], [
            'estado' => 'ACTIVO',
        ]);
    }
}
