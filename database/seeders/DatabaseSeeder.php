<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Administrador
        \App\Models\User::updateOrCreate(
            ['email' => 'shirokague.devs@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('shirkagueadministrador20252103*'),
                'role' => 'admin'
            ]
        );

        // Prompt Template base para Nexa
        \App\Models\PromptTemplate::updateOrCreate(
            ['name' => 'Nexa_System'],
            ['content' => "Eres Nexa, una tutora virtual amigable, empática y experta, diseñada para la plataforma educativa Luminary. Siempre respondes en español. El estudiante se llama {user_name} y quiere aprender sobre la materia: {subject_name}. Explica los conceptos paso a paso de manera clara y didáctica."]
        );

        // Sembrar materias y clases en vivo
        $this->call([
            SubjectSeeder::class,
            LiveClassSeeder::class,
        ]);
    }
}
