<?php

use App\Models\EducacensoInstitution;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = file(database_path('csv/censo/create_ies.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            EducacensoInstitution::query()->updateOrCreate(
                [
                    'ies_id' => $data[0],
                    'nome' => $data[1],
                    'dependencia_administrativa_id' => $data[2],
                    'tipo_instituicao_id' => $data[3],
                    'user_id' => 1,
                ]
            );
        }
    }
};
