<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosLaboratoriosEscola as MigraDadosLaboratoriosEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosLaboratoriosEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosLaboratoriosEscolaClass::execute();
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
