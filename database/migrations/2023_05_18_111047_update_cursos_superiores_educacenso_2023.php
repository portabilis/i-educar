<?php

use App\Models\EducacensoDegree;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
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
                'grau_academico' => $this->getGrauAcademico($data[8]),
            ]);
        }
    }

    private function getGrauAcademico(string $grau): int
    {
        switch(mb_strtoupper($grau)) {
            case 'TECNOLÓGICO':
                return EducacensoDegree::GRAU_TECNOLOGICO;
            case 'LICENCIATURA':
                return EducacensoDegree::GRAU_LICENCIATURA;
            case 'BACHARELADO':
                return EducacensoDegree::GRAU_BACHARELADO;
            case 'SEQUENCIAL':
                return EducacensoDegree::GRAU_SEQUENCIAL;
            default:
                return 0;
        }
    }
};
