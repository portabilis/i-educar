<?php

use App\Models\EducacensoDegree;
use Illuminate\Database\Migrations\Migration;

class MigrateEducacensoDegreeData2020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = file(database_path('csv/de_para_cursos_educacenso_2020.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            if (empty($data[3] || $data[4])) {
                continue;
            }

            $degree = EducacensoDegree::where('curso_id', $data[1])->first();
            $degree->curso_id = $data[3];
            $degree->nome = explode(' - ', $data[4])[0];
            $degree->save();
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
}
