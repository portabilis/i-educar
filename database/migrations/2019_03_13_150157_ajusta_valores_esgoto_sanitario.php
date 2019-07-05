<?php

use iEducar\Modules\Educacenso\Migrations\AjustaValoresAbastecimentoEnergia as AjustaValoresAbastecimentoEnergiaClass;
use Illuminate\Database\Migrations\Migration;

class AjustaValoresEsgotoSanitario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AjustaValoresAbastecimentoEnergiaClass::execute();
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
