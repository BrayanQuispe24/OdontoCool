<?php

namespace App\Services;

use App\Models\Analisis;
use App\Models\Doctor;
use App\Models\Propietario;
use App\Models\Secretaria;
use App\Models\Servicio;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PublicLandingService
{
    public function landingData(): array
    {
        $servicios = $this->buildServicios();
        $analisis = $this->buildAnalisis();
        $doctores = $this->buildDoctores();
        $clinica = $this->buildClinica($doctores, $servicios, $analisis);

        return [
            'servicios' => $servicios,
            'analisis' => $analisis,
            'doctores' => $doctores,
            'clinica' => $clinica,
        ];
    }

    public function search(string $query = ''): array
    {
        $data = $this->landingData();
        $queryNormalizado = $this->normalize($query);

        $index = collect()
            ->merge($this->buildServiceIndex($data['servicios']))
            ->merge($this->buildAnalisisIndex($data['analisis']))
            ->merge($this->buildDoctorIndex($data['doctores']))
            ->merge($this->buildClinicaIndex($data['clinica']));

        if ($queryNormalizado === '') {
            $results = collect()
                ->merge($index->where('section', 'doctores')->take(3))
                ->merge($index->where('section', 'servicios')->take(3))
                ->merge($index->where('section', 'analisis')->take(3))
                ->merge($index->where('section', 'clinica')->take(3))
                ->values();
        } else {
            $results = $index
                ->map(function (array $item) use ($queryNormalizado) {
                    $score = $this->scoreItem($item, $queryNormalizado);

                    if ($score === 0) {
                        return null;
                    }

                    $item['score'] = $score;

                    return $item;
                })
                ->filter()
                ->sortByDesc('score')
                ->take(12)
                ->values();
        }

        return [
            'query' => $query,
            'results' => $results->map(function (array $item) {
                return Arr::only($item, [
                    'type',
                    'title',
                    'description',
                    'anchor',
                    'section',
                    'badge',
                ]);
            })->all(),
            'total' => $results->count(),
        ];
    }

    private function buildServicios(): array
    {
        return Servicio::query()
            ->activo()
            ->orderBy('nombre')
            ->get()
            ->map(function (Servicio $servicio) {
                return [
                    'id' => $servicio->id,
                    'title' => $servicio->nombre,
                    'description' => $servicio->descripcion ?: 'Servicio odontológico disponible.',
                    'badge' => $servicio->tipo ?: 'Servicio',
                    'anchor' => 'servicio-'.$servicio->id,
                ];
            })
            ->values()
            ->all();
    }

    private function buildAnalisis(): array
    {
        return Analisis::query()
            ->activo()
            ->orderBy('nombre')
            ->get()
            ->map(function (Analisis $analisis) {
                return [
                    'id' => $analisis->id,
                    'title' => $analisis->nombre,
                    'description' => $analisis->descripcion ?: 'Análisis disponible en la clínica.',
                    'badge' => 'Análisis',
                    'anchor' => 'analisis-'.$analisis->id,
                ];
            })
            ->values()
            ->all();
    }

    private function buildDoctores(): array
    {
        return Doctor::query()
            ->with([
                'persona.usuarios',
                'especialidades',
                'turnos',
            ])
            ->orderBy('codigo_doctor')
            ->get()
            ->map(function (Doctor $doctor) {
                $persona = $doctor->persona;
                $usuario = $persona?->usuarios?->first();

                $especialidades = $doctor->especialidades
                    ->map(fn ($especialidad) => $especialidad->nombre)
                    ->filter()
                    ->values();

                $turnos = $doctor->turnos
                    ->map(function ($turno) {
                        $dia = $turno->pivot?->dia_semana ?: $turno->nombre;

                        return trim(sprintf('%s %s - %s', $dia, $turno->hora_inicio, $turno->hora_fin));
                    })
                    ->filter()
                    ->values();

                $nombreCompleto = trim(($persona?->nombre ?? '').' '.($persona?->apellido ?? ''));

                return [
                    'codigo_doctor' => $doctor->codigo_doctor,
                    'title' => $nombreCompleto !== '' ? $nombreCompleto : $doctor->codigo_doctor,
                    'description' => $this->buildDoctorDescription($doctor, $especialidades),
                    'anchor' => 'doctor-'.$doctor->codigo_doctor,
                    'photo' => $usuario?->url,
                    'telefono' => $doctor->telefono_profesional ?: $persona?->telefono,
                    'correo' => $usuario?->email,
                    'especialidades' => $especialidades->all(),
                    'informacion_adicional' => [
                        $doctor->matricula_profesional ? 'Matrícula '.$doctor->matricula_profesional : null,
                        $doctor->experiencia !== null ? $doctor->experiencia.' años de experiencia' : null,
                        $doctor->fecha_contratacion ? 'Contratado el '.$doctor->fecha_contratacion : null,
                    ],
                    'turnos' => $turnos->all(),
                ];
            })
            ->values()
            ->map(function (array $doctor) {
                $doctor['informacion_adicional'] = collect($doctor['informacion_adicional'])
                    ->filter()
                    ->values()
                    ->all();

                return $doctor;
            })
            ->all();
    }

    private function buildClinica(array $doctores, array $servicios, array $analisis): array
    {
        $propietario = Propietario::query()
            ->with(['persona.usuarios'])
            ->orderBy('codigo_propietario')
            ->first();

        $secretaria = Secretaria::query()
            ->with(['persona.usuarios'])
            ->orderBy('codigo_secretaria')
            ->first();

        $contactoPersona = $propietario?->persona
            ?? $secretaria?->persona
            ?? $this->firstDoctorPersona($doctores);

        $contactoUsuario = $contactoPersona?->usuarios?->first();

        $turnos = collect($doctores)
            ->flatMap(fn (array $doctor) => $doctor['turnos'])
            ->filter()
            ->unique()
            ->values();

        $turnosTexto = $turnos->isNotEmpty()
            ? $turnos->take(4)->implode(', ')
            : 'Horario sujeto a agenda clínica.';

        $especialidades = collect($doctores)
            ->flatMap(fn (array $doctor) => $doctor['especialidades'])
            ->filter()
            ->unique()
            ->values();

        $direccion = $contactoPersona?->direccion ?: 'Dirección no registrada en la base de datos.';
        $telefono = $contactoPersona?->telefono
            ?: collect($doctores)->pluck('telefono')->filter()->first()
            ?: 'No registrado';

        $correo = $contactoUsuario?->email
            ?: collect($doctores)->pluck('correo')->filter()->first()
            ?: 'No registrado';

        return [
            'nombre' => config('app.name', 'OdontoCool'),
            'descripcion' => 'Atención odontológica integral con servicios preventivos, diagnósticos y especializados, organizada desde datos reales del sistema.',
            'resumen' => [
                'servicios' => count($servicios),
                'analisis' => count($analisis),
                'doctores' => count($doctores),
                'especialidades' => $especialidades->count(),
            ],
            'cards' => [
                [
                    'type' => 'Información',
                    'title' => 'Horarios de atención',
                    'description' => $turnosTexto,
                    'anchor' => 'clinica-horarios',
                    'badge' => 'Horarios',
                ],
                [
                    'type' => 'Información',
                    'title' => 'Contacto',
                    'description' => sprintf('Teléfono: %s · Correo: %s', $telefono, $correo),
                    'anchor' => 'clinica-contacto',
                    'badge' => 'Contacto',
                ],
                [
                    'type' => 'Información',
                    'title' => 'Ubicación',
                    'description' => $direccion,
                    'anchor' => 'clinica-ubicacion',
                    'badge' => 'Ubicación',
                ],
                [
                    'type' => 'Información',
                    'title' => 'Cobertura clínica',
                    'description' => sprintf(
                        'Se atienden %d servicios odontológicos y %d análisis diagnósticos.',
                        count($servicios),
                        count($analisis)
                    ),
                    'anchor' => 'clinica-cobertura',
                    'badge' => 'Cobertura',
                ],
            ],
        ];
    }

    private function buildServiceIndex(array $servicios): Collection
    {
        return collect($servicios)->map(function (array $service) {
            return $this->buildSearchItem(
                'Servicio',
                $service['title'],
                $service['description'],
                $service['anchor'],
                'servicios',
                95,
                implode(' ', [
                    $service['title'],
                    $service['description'],
                    $service['badge'] ?? '',
                    'limpieza dental ortodoncia radiografía',
                ]),
                $service['badge'] ?? 'Servicio'
            );
        });
    }

    private function buildAnalisisIndex(array $analisis): Collection
    {
        return collect($analisis)->map(function (array $item) {
            return $this->buildSearchItem(
                'Análisis',
                $item['title'],
                $item['description'],
                $item['anchor'],
                'analisis',
                90,
                implode(' ', [
                    $item['title'],
                    $item['description'],
                    'radiografía ortodoncia diagnóstico',
                ]),
                $item['badge'] ?? 'Análisis'
            );
        });
    }

    private function buildDoctorIndex(array $doctores): Collection
    {
        return collect($doctores)->map(function (array $doctor) {
            $turnos = implode(' ', $doctor['turnos'] ?? []);
            $especialidades = implode(' ', $doctor['especialidades'] ?? []);
            $adicional = implode(' ', $doctor['informacion_adicional'] ?? []);

            return $this->buildSearchItem(
                'Doctor',
                $doctor['title'],
                $doctor['description'],
                $doctor['anchor'],
                'doctores',
                100,
                implode(' ', [
                    $doctor['title'],
                    $doctor['telefono'] ?? '',
                    $doctor['correo'] ?? '',
                    $turnos,
                    $especialidades,
                    $adicional,
                ]),
                null
            );
        });
    }

    private function buildClinicaIndex(array $clinica): Collection
    {
        return collect($clinica['cards'] ?? [])->map(function (array $card) {
            return $this->buildSearchItem(
                'Información',
                $card['title'],
                $card['description'],
                $card['anchor'],
                'clinica',
                85,
                implode(' ', [
                    $card['title'],
                    $card['description'],
                    'horarios contacto ubicación especialidades limpieza dental ortodoncia radiografía',
                ]),
                $card['badge'] ?? 'Información'
            );
        });
    }

    private function buildSearchItem(
        string $type,
        string $title,
        string $description,
        string $anchor,
        string $section,
        int $priority,
        string $searchable,
        ?string $badge = null,
    ): array {
        return [
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'anchor' => $anchor,
            'section' => $section,
            'priority' => $priority,
            'badge' => $badge,
            'searchable' => $this->normalize($searchable),
        ];
    }

    private function buildDoctorDescription(Doctor $doctor, Collection $especialidades): string
    {
        $fragments = [];

        if ($especialidades->isNotEmpty()) {
            $fragments[] = 'Especialidad: '.$especialidades->implode(', ');
        }

        if ($doctor->telefono_profesional) {
            $fragments[] = 'Teléfono profesional '.$doctor->telefono_profesional;
        }

        if ($doctor->experiencia !== null) {
            $fragments[] = $doctor->experiencia.' años de experiencia';
        }

        return $fragments !== []
            ? implode(' · ', $fragments)
            : 'Información profesional disponible en la clínica.';
    }

    private function firstDoctorPersona(array $doctores): mixed
    {
        $firstDoctor = $doctores[0] ?? null;

        if (! $firstDoctor || ! is_array($firstDoctor)) {
            return null;
        }

        return $firstDoctor['persona'] ?? null;
    }

    private function scoreItem(array $item, string $query): int
    {
        if (! str_contains($item['searchable'], $query)) {
            return 0;
        }

        $score = $item['priority'];

        if (str_contains($this->normalize($item['title']), $query)) {
            $score += 25;
        }

        if (str_contains($this->normalize($item['description']), $query)) {
            $score += 10;
        }

        return $score;
    }

    private function normalize(?string $value): string
    {
        return Str::of($value ?? '')
            ->ascii()
            ->lower()
            ->squish()
            ->toString();
    }
}
