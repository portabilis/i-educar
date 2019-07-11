<?php

use iEducar\Modules\Educacenso\Migrations\AtualizaValoresEscolaridadeServidorEducacenso as AtualizaValoresEscolaridadeServidorEducacensoClass;
use Illuminate\Database\Migrations\Migration;

class AtualizaValoresEscolaridadeServidorEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AtualizaValoresEscolaridadeServidorEducacensoClass::execute();
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
