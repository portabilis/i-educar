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
        $file = file(database_path('csv/censo/2022/update_names_ies_2022.csv'));

        foreach ($file as $line) {
            $data = str_getcsv($line);

            $eis = EducacensoInstitution::query()->where('ies_id', $data[0])->first();
            $eis->nome = $data[1];
            $eis->save();
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
