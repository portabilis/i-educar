<?php

use iEducar\Modules\Educacenso\Migrations\RemoveDeprecatedEtapaEducacensoFromTurmas as RemoveDeprecatedEtapaEducacensoFromTurmasClass;
use Illuminate\Database\Migrations\Migration;

class RemoveDeprecatedEtapaEducacensoFromTurmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        RemoveDeprecatedEtapaEducacensoFromTurmasClass::execute();
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
