<?php

use iEducar\Modules\Educacenso\Migrations\InsereDadosPredioCompartilhadoOutraEscola as InsereDadosPredioCompartilhadoOutraEscolaClass;
use Illuminate\Database\Migrations\Migration;

class InsereDadosPredioCompartilhadoOutraEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InsereDadosPredioCompartilhadoOutraEscolaClass::execute();
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
