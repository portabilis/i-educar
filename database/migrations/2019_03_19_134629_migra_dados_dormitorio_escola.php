<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosDormitorioEscola as MigraDadosDormitorioEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosDormitorioEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosDormitorioEscolaClass::execute();
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
