<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveServidorDisciplinaFuncaoIsNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DELETE FROM pmieducar.servidor_disciplina WHERE ref_cod_funcao IS NULL');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
