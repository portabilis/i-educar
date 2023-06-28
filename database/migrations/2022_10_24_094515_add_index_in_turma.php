<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->index('turma_turno_id');
            $table->index('ref_ref_cod_escola');
            $table->index('ref_ref_cod_serie');
            $table->index('ref_cod_curso');
        });
    }

    public function down()
    {
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_turma_turma_turno_id_index;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_turma_ref_ref_cod_escola_index;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_turma_ref_ref_cod_serie_index;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_turma_ref_cod_curso_index;');
    }
};
