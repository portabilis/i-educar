<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        DB::table('cadastro.escolaridade')
            ->where('escolaridade', 5)
            ->update([
                'escolaridade' => 7
            ]);
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
