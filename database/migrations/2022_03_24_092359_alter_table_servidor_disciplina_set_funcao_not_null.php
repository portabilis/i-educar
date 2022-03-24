<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableServidorDisciplinaSetFuncaoNotNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::unprepared('ALTER TABLE pmieducar.servidor_disciplina ALTER COLUMN ref_cod_funcao SET NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
