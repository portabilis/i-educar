<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasGeraisEscola as MigraDadosSalasGeraisEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosSalasGeraisEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosSalasGeraisEscolaClass::execute();
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
