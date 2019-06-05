<?php

use iEducar\Modules\Educacenso\Migrations\MigraTipoAtendimentoDaTurmaEducacenso as MigraTipoAtendimentoDaTurmaEducacensoClass;
use Illuminate\Database\Migrations\Migration;

class MigraTipoAtendimentoDaTurmaEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraTipoAtendimentoDaTurmaEducacensoClass::execute();
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
