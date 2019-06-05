<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasFuncionaisEscola as MigraDadosSalasFuncionaisEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosSalasFuncionaisEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosSalasFuncionaisEscolaClass::execute();
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