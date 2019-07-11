<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosAreasExternasEscola as MigraDadosAreasExternasEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosAreasExternasEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosAreasExternasEscolaClass::execute();
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
