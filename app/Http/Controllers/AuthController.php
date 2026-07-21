<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterAuthRequest;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\Paciente;
use App\Models\Persona;
use App\Models\Rol;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $data = $request->validated();
        
        try {
            //usuario existente
            $usuarioExistente = User::where('email', $data['email'])->first();
            if ($usuarioExistente) {
                return back()->withErrors([
                    'email' => 'El correo electrónico ya está registrado. Inicie sesión o contactese con el administrador del sistema',
                ])->withInput();
            }

            DB::beginTransaction();

            $dataPersona = collect($data)->only([
                'carnet_identidad',
                'nombre',
                'apellido',
                'fecha_nacimiento',
                'direccion',
                'genero',
                'telefono',
            ])->toArray();

            $persona = Persona::create($dataPersona);

            $codigoPaciente = 'PAC-' . str_pad($persona->carnet_identidad, 10, '0', STR_PAD_LEFT);

            $dataPaciente = collect($data)->only([
                'contacto_emergencia',
                'telefono_emergencia',
            ])->toArray();

            $dataPaciente['codigo_paciente'] = $codigoPaciente;
            $dataPaciente['persona_id'] = $persona->carnet_identidad;

            $paciente = Paciente::create($dataPaciente);

            $dataUsuario = collect($data)->only([
                'email',
                'url',
                'password',
            ])->toArray();
            
            $rol_id = Rol::where('nombre', 'Paciente')->first()->id;
            $codigoUsuario = 'USR-' . str_pad($persona->carnet_identidad, 10, '0', STR_PAD_LEFT);
            
            $dataUsuario['codigo_usuario'] = $codigoUsuario;
            $dataUsuario['persona_id'] = $persona->carnet_identidad;
            $dataUsuario['password'] = Hash::make($data['password']);
            $dataUsuario['rol_id'] = $rol_id;
            
            $user = User::create($dataUsuario);

            DB::commit();

            // Autenticar al usuario después del registro
            Auth::login($user);

            if($user->rol?->nombre === 'Paciente'){
                $paciente = Paciente::where('persona_id', $user->persona_id)->first();
                if (!$paciente) {
                    return back()->withErrors([
                        'general' => 'Error al encontrar el paciente asociado al usuario.',
                    ])->withInput();
                }
                return redirect(route('citas.index', ['codigo_paciente' => $paciente->codigo_paciente], absolute: false));
            }

            return redirect(route('citas.index', absolute: false));
            
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'general' => 'Error al registrar el usuario: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function login(LoginAuthRequest $request)
    {
        try {
            $data = $request->validated();
            // \Log::info('Login - Intentando login:', ['email' => $data['email']]);
            
            // Buscar usuario por email
            $user = User::where('email', $data['email'])->first();
            
            // Validar credenciales
            if (!$user || !Hash::check($data['password'], $user->password)) {
                // \Log::warning('Login - Credenciales incorrectas:', ['email' => $data['email']]);
                return back()->withErrors([
                    'email' => 'Las credenciales no coinciden con nuestros registros.',
                ])->withInput($request->only('email'));
            }

            // Validar que el usuario esté activo
            if ($user->estado !== 'activo') {
                // \Log::warning('Login - Usuario inactivo:', ['email' => $data['email']]);
                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.',
                ])->withInput($request->only('email'));
            }

            // Autenticar el usuario
            Auth::login($user, $data['remember'] ?? false);
            // \Log::info('Login - Usuario autenticado:', ['email' => $data['email'], 'codigo_usuario' => $user->codigo_usuario]);

            // Regenerar sesión para prevenir session fixation
            $request->session()->regenerate();

            if ($user->rol?->nombre === 'Paciente') {
                return redirect(route('citas.index', absolute: false));
            }
            if ($user->rol?->nombre === 'Secretaria') {
                return redirect(route('citas.index', absolute: false));
            }
            if($user->rol?->nombre === 'Doctor'){
                return redirect(route('doctor.index', absolute: false));
            }
            return redirect(route('reportes.index', absolute: false));
            
        } catch (\Exception $e) {
            // \Log::error('Login - Error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors([
                'general' => 'Error al iniciar sesión: ' . $e->getMessage(),
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        try {
            // \Log::info('Logout - Usuario cerrando sesión:', ['usuario' => Auth::user()?->codigo_usuario]);
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // \Log::info('Logout - Sesión invalidada exitosamente');
            
            return redirect(route('login', absolute: false));
        } catch (\Exception $e) {
            // \Log::error('Logout - Error:', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'general' => 'Error al cerrar sesión',
            ]);
        }
    }

    public function logoutAll(Request $request)
    {
        try {
            // \Log::info('LogoutAll - Llamado (no aplica para sesiones web)');
            
            // Para sesiones web, logout y logoutAll es lo mismo
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect(route('login', absolute: false));
        } catch (\Exception $e) {
            // \Log::error('LogoutAll - Error:', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'general' => 'Error al cerrar sesión',
            ]);
        }
    }
}
