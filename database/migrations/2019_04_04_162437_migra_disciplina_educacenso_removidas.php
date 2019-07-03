<?php

use iEducar\Modules\Educacenso\Migrations\MigraDisciplinaEducacensoRemovidas as MigraDisciplinaEducacensoRemovidasClass;
use Illuminate\Database\Migrations\Migration;

class MigraDisciplinaEducacensoRemovidas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDisciplinaEducacensoRemovidasClass::execute();
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
