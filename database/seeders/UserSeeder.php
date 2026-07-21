<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'nombre');

        $users = [
            ['codigo_usuario' => 'USR001', 'email' => 'admin@odontocool.com', 'password' => 'password123', 'estado' => 'activo', 'url' => null, 'rol' => 'Administrador', 'persona_id' => '1000001'],
            ['codigo_usuario' => 'USR002', 'email' => 'paciente@odontocool.com', 'password' => 'password123', 'estado' => 'activo', 'url' => null, 'rol' => 'Paciente', 'persona_id' => '1000002'],
            ['codigo_usuario' => 'USR003', 'email' => 'secretaria@odontocool.com', 'password' => 'password123', 'estado' => 'activo', 'url' => null, 'rol' => 'Secretaria', 'persona_id' => '1000006'],
            ['codigo_usuario' => 'USR004', 'email' => 'doctor@odontocool.com', 'password' => 'password123', 'estado' => 'activo', 'url' => null, 'rol' => 'Doctor', 'persona_id' => '1000008'],
            ['codigo_usuario' => 'USR005', 'email' => 'propietario@odontocool.com', 'password' => 'password123', 'estado' => 'activo', 'url' => null, 'rol' => 'Propietario', 'persona_id' => '1000009'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['codigo_usuario' => $user['codigo_usuario']],
                [
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'estado' => $user['estado'],
                    'url' => $user['url'],
                    'rol_id' => $roles[$user['rol']],
                    'persona_id' => $user['persona_id'],
                ]
            );
        }
    }
}