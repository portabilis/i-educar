<?php

use iEducar\Modules\Educacenso\Migrations\RemoveValoresInvalidosDaLocalizacaoDiferenciada as RemoveValoresInvalidosDaLocalizacaoDiferenciadaClass;
use Illuminate\Database\Migrations\Migration;

class RemoveValoresInvalidosDaLocalizacaoDiferenciada extends Migration
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
}
