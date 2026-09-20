<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subject = \App\Models\Subject::where('name', 'Física')->first();

        if ($subject) {
            \App\Models\LiveClass::updateOrCreate(
                ['title' => 'Laboratorio de ondas'],
                [
                    'subject_id' => $subject->id,
                    'scheduled_at' => now()->addMinutes(30),
                    'status' => 'pending',
                ]
            );
        }
    }
}
