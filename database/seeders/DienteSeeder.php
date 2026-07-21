<?php

namespace Database\Seeders;

use App\Models\Diente;
use Illuminate\Database\Seeder;

class DienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dientes = [
            // Superior derecho
            ['nombre' => 'Incisivo central superior derecho', 'numero' => 11, 'tipo' => 'Incisivo', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Incisivo lateral superior derecho', 'numero' => 12, 'tipo' => 'Incisivo', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Canino superior derecho', 'numero' => 13, 'tipo' => 'Canino', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Primer premolar superior derecho', 'numero' => 14, 'tipo' => 'Premolar', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Segundo premolar superior derecho', 'numero' => 15, 'tipo' => 'Premolar', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Primer molar superior derecho', 'numero' => 16, 'tipo' => 'Molar', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Segundo molar superior derecho', 'numero' => 17, 'tipo' => 'Molar', 'ubicacion' => 'Superior derecho'],
            ['nombre' => 'Tercer molar superior derecho', 'numero' => 18, 'tipo' => 'Molar', 'ubicacion' => 'Superior derecho'],

            // Superior izquierdo
            ['nombre' => 'Incisivo central superior izquierdo', 'numero' => 21, 'tipo' => 'Incisivo', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Incisivo lateral superior izquierdo', 'numero' => 22, 'tipo' => 'Incisivo', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Canino superior izquierdo', 'numero' => 23, 'tipo' => 'Canino', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Primer premolar superior izquierdo', 'numero' => 24, 'tipo' => 'Premolar', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Segundo premolar superior izquierdo', 'numero' => 25, 'tipo' => 'Premolar', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Primer molar superior izquierdo', 'numero' => 26, 'tipo' => 'Molar', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Segundo molar superior izquierdo', 'numero' => 27, 'tipo' => 'Molar', 'ubicacion' => 'Superior izquierdo'],
            ['nombre' => 'Tercer molar superior izquierdo', 'numero' => 28, 'tipo' => 'Molar', 'ubicacion' => 'Superior izquierdo'],

            // Inferior izquierdo
            ['nombre' => 'Incisivo central inferior izquierdo', 'numero' => 31, 'tipo' => 'Incisivo', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Incisivo lateral inferior izquierdo', 'numero' => 32, 'tipo' => 'Incisivo', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Canino inferior izquierdo', 'numero' => 33, 'tipo' => 'Canino', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Primer premolar inferior izquierdo', 'numero' => 34, 'tipo' => 'Premolar', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Segundo premolar inferior izquierdo', 'numero' => 35, 'tipo' => 'Premolar', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Primer molar inferior izquierdo', 'numero' => 36, 'tipo' => 'Molar', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Segundo molar inferior izquierdo', 'numero' => 37, 'tipo' => 'Molar', 'ubicacion' => 'Inferior izquierdo'],
            ['nombre' => 'Tercer molar inferior izquierdo', 'numero' => 38, 'tipo' => 'Molar', 'ubicacion' => 'Inferior izquierdo'],

            // Inferior derecho
            ['nombre' => 'Incisivo central inferior derecho', 'numero' => 41, 'tipo' => 'Incisivo', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Incisivo lateral inferior derecho', 'numero' => 42, 'tipo' => 'Incisivo', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Canino inferior derecho', 'numero' => 43, 'tipo' => 'Canino', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Primer premolar inferior derecho', 'numero' => 44, 'tipo' => 'Premolar', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Segundo premolar inferior derecho', 'numero' => 45, 'tipo' => 'Premolar', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Primer molar inferior derecho', 'numero' => 46, 'tipo' => 'Molar', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Segundo molar inferior derecho', 'numero' => 47, 'tipo' => 'Molar', 'ubicacion' => 'Inferior derecho'],
            ['nombre' => 'Tercer molar inferior derecho', 'numero' => 48, 'tipo' => 'Molar', 'ubicacion' => 'Inferior derecho'],
        ];

        foreach ($dientes as $diente) {
            Diente::create($diente);
        }
    }
}
