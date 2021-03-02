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
        $file = file(database_path('csv/cursos_educacenso_2020.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            if (EducacensoDegree::where('curso_id', $data[1])->exists()) {
                continue;
            }

            EducacensoDegree::create([
                'curso_id' => $data[1],
                'nome' => explode(' - ', $data[2])[0],
                'classe_id' => $data[0],
                'user_id' => 1,
                'created_at' => now(),
                'grau_academico' => $this->getLevel($data[3]),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function getLevel($id)
    {
        $levels = [
            'Tecnológico' => EducacensoDegree::GRAU_TECNOLOGICO,
            'Licenciatura' => EducacensoDegree::GRAU_LICENCIATURA,
            'Bacharelado' => EducacensoDegree::GRAU_BACHARELADO,
            'Sequencial' => EducacensoDegree::GRAU_SEQUENCIAL,
        ];

        return $levels[$id];
    }
}
