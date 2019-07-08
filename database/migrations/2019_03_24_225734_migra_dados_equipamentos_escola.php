<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosEquipamentosEscola as MigraDadosEquipamentosEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosEquipamentosEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosEquipamentosEscolaClass::execute();
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
