<?php

use App\Models\EducacensoDegree;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = file(database_path('csv/censo/2022/create_curso_superior_2022.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            EducacensoDegree::query()->updateOrCreate(
                [
                    'curso_id' => $data[0],
                    'nome' => $data[1],
                    'grau_academico' => $data[2],
                    'classe_id' => $data[3],
                    'user_id' => 1
                ]
            );
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
};
