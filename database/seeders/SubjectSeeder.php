<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
            ['name' => 'Física', 'description' => 'Movimiento armónico, termodinámica', 'is_active' => true],
            ['name' => 'Matemáticas', 'description' => 'Funciones cuadráticas, cálculo', 'is_active' => true],
            ['name' => 'Química', 'description' => 'Reacciones redox, enlaces', 'is_active' => true],
            ['name' => 'Biología', 'description' => 'Genética molecular', 'is_active' => true],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::updateOrCreate(['name' => $subject['name']], $subject);
        }
    }
}
