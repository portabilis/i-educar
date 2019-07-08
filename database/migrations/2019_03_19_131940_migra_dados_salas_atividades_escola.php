<?php

use iEducar\Modules\Educacenso\Migrations\MigraDadosSalasAtividadesEscola as MigraDadosSalasAtividadesEscolaClass;
use Illuminate\Database\Migrations\Migration;

class MigraDadosSalasAtividadesEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MigraDadosSalasAtividadesEscolaClass::execute();
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
