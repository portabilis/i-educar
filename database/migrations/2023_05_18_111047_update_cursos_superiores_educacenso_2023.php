<?php

use App\Models\EducacensoDegree;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    public function up(): void
    {
        $file = file(database_path('csv/censo/2023/update_or_create_cursos_superiores_2023.csv'));

        foreach ($file as $line) {
            $data = str_getcsv(
                string: $line,
                separator: ';'
            );

            EducacensoDegree::updateOrCreate([
                'curso_id' => $data[6],
            ], [
                'nome' => $data[7],
                'classe_id' => $data[0],
                'user_id' => 1,
                'grau_academico' => match (mb_strtoupper($data[8])) {
                    'TECNOLÓGICO' => EducacensoDegree::GRAU_TECNOLOGICO,
                    'LICENCIATURA' => EducacensoDegree::GRAU_LICENCIATURA,
                    'BACHARELADO' => EducacensoDegree::GRAU_BACHARELADO,
                    'SEQUENCIAL' => EducacensoDegree::GRAU_SEQUENCIAL,
                    default => 0,
                },
            ]);
        }
    }
};
