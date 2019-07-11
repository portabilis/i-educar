<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosBanheirosEscola as MigraDadosBanheirosEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosBanheirosEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosBanheirosEscolaClass::execute();
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
