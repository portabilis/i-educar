<?php

use App\Models\EducacensoInstitution;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = file(database_path('csv/censo/2022/create_ies_2022.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            EducacensoInstitution::query()->updateOrCreate(
                [
                    'ies_id' => $data[0],
                    'nome' => $data[1],
                    'dependencia_administrativa_id' => $data[2],
                    'tipo_instituicao_id' => $data[0],
                    'uf' => $data[4],
                    'user_id' => 1,
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
