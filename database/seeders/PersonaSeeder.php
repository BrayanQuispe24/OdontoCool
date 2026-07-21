<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $personas = [
            ['carnet_identidad' => '1000001', 'nombre' => 'Carlos', 'apellido' => 'Rojas', 'fecha_nacimiento' => '1990-05-12', 'direccion' => 'Barrio Centro', 'genero' => 'Masculino', 'telefono' => '70000001'],
            ['carnet_identidad' => '1000002', 'nombre' => 'Maria', 'apellido' => 'Lopez', 'fecha_nacimiento' => '1995-08-21', 'direccion' => 'Av. Banzer', 'genero' => 'Femenino', 'telefono' => '70000002'],
            ['carnet_identidad' => '1000003', 'nombre' => 'Luis', 'apellido' => 'Vargas', 'fecha_nacimiento' => '1988-03-10', 'direccion' => 'Plan 3000', 'genero' => 'Masculino', 'telefono' => '70000003'],
            ['carnet_identidad' => '1000004', 'nombre' => 'Ana', 'apellido' => 'Mendoza', 'fecha_nacimiento' => '1999-11-05', 'direccion' => 'Equipetrol', 'genero' => 'Femenino', 'telefono' => '70000004'],
            ['carnet_identidad' => '1000005', 'nombre' => 'Jorge', 'apellido' => 'Suarez', 'fecha_nacimiento' => '1985-01-17', 'direccion' => 'Villa Primero de Mayo', 'genero' => 'Masculino', 'telefono' => '70000005'],
            ['carnet_identidad' => '1000006', 'nombre' => 'Lucia', 'apellido' => 'Flores', 'fecha_nacimiento' => '1992-07-30', 'direccion' => 'Los Tusequis', 'genero' => 'Femenino', 'telefono' => '70000006'],
            ['carnet_identidad' => '1000007', 'nombre' => 'Pedro', 'apellido' => 'Gomez', 'fecha_nacimiento' => '1991-09-15', 'direccion' => 'Radial 26', 'genero' => 'Masculino', 'telefono' => '70000007'],
            ['carnet_identidad' => '1000008', 'nombre' => 'Sofia', 'apellido' => 'Castro', 'fecha_nacimiento' => '1996-12-22', 'direccion' => 'La Guardia', 'genero' => 'Femenino', 'telefono' => '70000008'],
            ['carnet_identidad' => '1000009', 'nombre' => 'Miguel', 'apellido' => 'Torres', 'fecha_nacimiento' => '1987-04-19', 'direccion' => 'El Trompillo', 'genero' => 'Masculino', 'telefono' => '70000009'],
            ['carnet_identidad' => '1000010', 'nombre' => 'Camila', 'apellido' => 'Fernandez', 'fecha_nacimiento' => '2000-02-14', 'direccion' => 'Santos Dumont', 'genero' => 'Femenino', 'telefono' => '70000010'],
        ];

        foreach ($personas as $persona) {
            Persona::updateOrCreate(
                ['carnet_identidad' => $persona['carnet_identidad']],
                $persona
            );
        }
    }
}