<?php

use App\Models\EducacensoDegree;
use Illuminate\Database\Migrations\Migration;

class NewEducacensoDegree2020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = file(database_path('csvs/educacenso/create_cursos.csv'));

        foreach ($file as $line) {
            $data = str_getcsv(
                string: $line,
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
}
