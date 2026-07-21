<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            ModuloSeeder::class,
            PermisoSeeder::class,
            RolSeeder::class,
            RolPermisoSeeder::class,
            PersonaSeeder::class,
            DoctorSeeder::class,
            PropietarioSeeder::class,
            SecretariaSeeder::class,
            PacienteSeeder::class,
            UserSeeder::class,
            DienteSeeder::class,
            ServicioSeeder::class,
            ClinicaDatosSeeder::class,
            AnalisisSeeder::class,
            EstadoCitaSeeder::class,
            MetodoPagoSeeder::class,
            ModoPagoSeeder::class,
        ]);
    }
}
