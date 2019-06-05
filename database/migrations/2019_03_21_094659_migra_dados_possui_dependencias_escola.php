<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosPossuiDependenciasEscola as MigraDadosPossuiDependenciasEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosPossuiDependenciasEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosPossuiDependenciasEscolaClass::execute();
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
