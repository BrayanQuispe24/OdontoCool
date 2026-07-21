<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterAuthRequest;
use App\Models\Paciente;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterAuthRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            // 1. Crear la Persona
            $persona = Persona::create([
                'carnet_identidad' => $request->carnet_identidad,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion ?? 'Sin dirección',
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            // 2. Generar codigo_paciente (PAC_<carnet>)
            $codigoPaciente = 'PAC_' . $persona->carnet_identidad;

            // 3. Crear el Paciente
            $paciente = Paciente::create([
                'codigo_paciente' => $codigoPaciente,
                'persona_id' => $persona->carnet_identidad,
                'contacto_emergencia' => $request->contacto_emergencia,
                'telefono_emergencia' => $request->telefono_emergencia,
            ]);

            // 4. Obtener el rol de Paciente (o usar el rol_id si se envía)
            $rolPaciente = Rol::find($request->input('rol_id', 2)); // default a rol_id 2 si no se envía
            if (!$rolPaciente) {
                $rolPaciente = Rol::where('nombre', 'Paciente')->firstOrFail();
            }

            // 5. Generar codigo_usuario (USR_<carnet>)
            $codigoUsuario = 'USR_' . $persona->carnet_identidad;

            // 6. Crear el Usuario
            $user = User::create([
                'codigo_usuario' => $codigoUsuario,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'estado' => 'activo',
                'url' => $request->url ?? null,
                'rol_id' => $rolPaciente->id,
                'persona_id' => $persona->carnet_identidad,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        });
    }
}
