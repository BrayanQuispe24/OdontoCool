<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;
use App\Models\Precio;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->crearServicioConPrecio("Consulta odontológica general",
                                "Evaluación inicial del estado bucal del paciente.",
                                "Consulta", "BOB", "80");
        $this->crearServicioConPrecio("Limpieza dental", "Profilaxis dental para remover placa bacteriana y sarro.",
                                "Preventivo", "BOB", "150");
        $this->crearServicioConPrecio("Aplicación de flúor",
                                "Tratamiento preventivo para fortalecer el esmalte dental.",
                                "Preventivo", "BOB", "100");
        $this->crearServicioConPrecio("Sellantes dentales", "Aplicación de sellantes para prevenir caries.",
                                "Preventivo",
                                "BOB", "120");
        $this->crearServicioConPrecio("Restauración dental", "Curación de caries con resina u otro material.",
                                "Operatoria",
                                "BOB", "180");
        $this->crearServicioConPrecio("Extracción dental simple",
                                "Extracción de una pieza dental sin cirugía compleja.",
                                "Cirugía", "BOB", "200");
        $this->crearServicioConPrecio("Extracción de tercer molar", "Extracción de muela del juicio.", "Cirugía",
                                "BOB",
                                "450");
        $this->crearServicioConPrecio("Endodoncia", "Tratamiento de conductos radiculares.", "Endodoncia", "BOB",
                                "500");
        $this->crearServicioConPrecio("Blanqueamiento dental", "Procedimiento estético para aclarar dientes.",
                                "Estética",
                                "BOB", "700");
        $this->crearServicioConPrecio("Ortodoncia", "Tratamiento para corregir la posición dental.", "Ortodoncia",
                                "BOB",
                                "2500");
        $this->crearServicioConPrecio("Control de ortodoncia", "Revisión y ajuste periódico de ortodoncia.",
                                "Ortodoncia",
                                "BOB", "150");
        $this->crearServicioConPrecio("Periodoncia", "Tratamiento de encías y tejidos de soporte dental.",
                                "Periodoncia",
                                "BOB", "300");
        $this->crearServicioConPrecio("Raspado y alisado radicular", "Limpieza profunda debajo de la encía.",
                                "Periodoncia",
                                "BOB", "350");
        $this->crearServicioConPrecio("Prótesis dental", "Rehabilitación mediante prótesis removible o fija.",
                                "Rehabilitación", "BOB", "1200");
        $this->crearServicioConPrecio("Corona dental", "Restauración fija que cubre una pieza dental dañada.",
                                "Rehabilitación", "BOB", "900");
        $this->crearServicioConPrecio("Implante dental", "Reemplazo de una pieza dental mediante implante.",
                                "Implantología",
                                "BOB", "5000");
        $this->crearServicioConPrecio("Radiografía dental", "Estudio radiográfico para diagnóstico odontológico.",
                                "Diagnóstico", "BOB", "100");
        $this->crearServicioConPrecio("Urgencia odontológica",
                                "Atención inmediata por dolor, inflamación o traumatismo dental.", "Urgencia", "BOB",
                                "120");
    }

    private function crearServicioConPrecio(string $nombre, ?string $descripcion, string $tipo, string $moneda, string $monto): void
    {
        $servicio = Servicio::updateOrCreate([
            'nombre' => $nombre,
        ], [
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'estado' => 'ACTIVO',
        ]);

        $precio = Precio::firstOrCreate([
            'monto' => $monto,
            'moneda' => $moneda,
        ], [
            'estado' => 'activo'
        ]);

        $servicio->asignacionesPrecio()->updateOrCreate([
            'precio_id' => $precio->id,
            'fecha_inicio' => '2026-01-01',
        ], [
            'fecha_fin' => '2035-12-31',
            'estado' => 'ACTIVO',
        ]);
    }
}
