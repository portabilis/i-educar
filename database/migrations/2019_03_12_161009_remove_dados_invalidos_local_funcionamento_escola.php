<?php

use iEducar\Modules\Educacenso\Migrations\RemoveValoresInvalidosDaLocalizacaoDiferenciada as RemoveValoresInvalidosDaLocalizacaoDiferenciadaClass;
use Illuminate\Database\Migrations\Migration;

class RemoveDadosInvalidosLocalFuncionamentoEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        RemoveValoresInvalidosDaLocalizacaoDiferenciadaClass::execute();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
