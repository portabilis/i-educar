<?php

use iEducar\Modules\Educacenso\Migrations\UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019 as UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019Class;
use Illuminate\Database\Migrations\Migration;

class UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UpdateEscolaridadeEducacensoValuesToEducacensoLayout2019Class::execute();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cadastro.escolaridade')
            ->where('escolaridade', 7)
            ->update([
                'escolaridade' => 5
            ]);
    }
}
