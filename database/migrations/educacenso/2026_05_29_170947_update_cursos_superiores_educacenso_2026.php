<?php

use App\Models\EducacensoDegree;
use App\Models\EmployeeGraduation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $file = file(database_path('csv/censo/2026/update_or_create_cursos_superiores_2026.csv'));

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

        $ids = EducacensoDegree::query()
            ->select('id')
            ->whereIn('curso_id', [
                '0724E032',
                '0725P023',
                '0811A032',
                '0811A033',
            ])->get()->pluck('id')->toArray();

        EmployeeGraduation::query()
            ->whereIn('course_id', $ids)
            ->delete();

        EducacensoDegree::query()
            ->whereIn('id', $ids)
            ->delete();
    }
};
