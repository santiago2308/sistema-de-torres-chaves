<?php

namespace Database\Seeders;

use App\Models\Service;
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
        $services = [
            [
                'name' => 'Sonido Básico',
                'category' => 'Sonido',
                'price' => 120000,
                'description' => '2 parlantes, consola de sonido, 2 micrófonos y operador durante el evento.',
            ],
            [
                'name' => 'Sonido Profesional',
                'category' => 'Sonido',
                'price' => 220000,
                'description' => 'Sistema de sonido de mayor potencia, subwoofers, consola digital, 4 micrófonos y operador técnico.',
            ],
            [
                'name' => 'Iluminación Básica',
                'category' => 'Iluminación',
                'price' => 100000,
                'description' => '4 luces LED, 2 luces móviles y controlador de iluminación.',
            ],
            [
                'name' => 'Iluminación Profesional',
                'category' => 'Iluminación',
                'price' => 250000,
                'description' => '8 luces LED, 4 luces móviles, máquinas de humo y operador de iluminación.',
            ],
            [
                'name' => 'DJ + Sonido',
                'category' => 'DJ',
                'price' => 280000,
                'description' => 'DJ, consola, sistema de sonido profesional, micrófonos y musicalización durante el evento.',
            ],
            [
                'name' => 'Pack Fiesta Completa',
                'category' => 'Packs',
                'price' => 450000,
                'description' => 'Sonido profesional, iluminación profesional, DJ, máquinas de humo y operadores técnicos.',
            ],
            [
                'name' => 'Pantalla LED',
                'category' => 'Pantalla LED',
                'price' => 300000,
                'description' => 'Pantalla LED, estructura de montaje, conexión audiovisual y técnico encargado de la instalación.',
            ],
            [
                'name' => 'Efectos Especiales',
                'category' => 'Efectos',
                'price' => 180000,
                'description' => 'Máquina de humo, luces láser, efectos de iluminación y máquinas de burbujas.',
            ],
            [
                'name' => 'Pack Evento Premium',
                'category' => 'Packs',
                'price' => 650000,
                'description' => 'Sonido profesional, iluminación completa, DJ, pantalla LED, efectos especiales y equipo técnico.',
            ],
            [
                'name' => 'Sonido para Conferencias',
                'category' => 'Sonido',
                'price' => 160000,
                'description' => 'Micrófonos inalámbricos, parlantes, consola, reproducción de presentaciones y operador técnico.',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}