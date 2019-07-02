<?php

use iEducar\Modules\Educacenso\Migrations\MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado as MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursadoClass;
use Illuminate\Database\Migrations\Migration;

class MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigrateValuesFromEscolaridadeToTipoEnsinoMedioCursadoClass::execute();
    }
}
