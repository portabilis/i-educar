<?php

use iEducar\Modules\Educacenso\Migrations\UpdateDeficienciaEducacensoValuesForLayout2019 as UpdateDeficienciaEducacensoValuesForLayout2019Alias;
use Illuminate\Database\Migrations\Migration;

class UpdateDeficienciaEducacensoValuesForLayout2019 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UpdateDeficienciaEducacensoValuesForLayout2019Alias::execute();
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
