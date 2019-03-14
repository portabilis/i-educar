<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraPrimaryKeyMatriculaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove dependencia
        DB::statement('alter table pmieducar.matricula_excessao drop constraint if exists matricula_excessao_ref_cod_matricula_fkey;');

        // Remove primary key antiga
        DB::statement('alter table pmieducar.matricula_turma drop constraint  if exists matricula_turma_pkey;');

        // Cria indice unico
        DB::statement('create unique index if not exists matricula_turma_uindex_matricula_turma_sequencial 
                                on pmieducar.matricula_turma (ref_cod_matricula, ref_cod_turma, sequencial);');

        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropIndex('pmieducar.matricula_turma_uindex_matricula_turma_sequencial');
            $table->primary(['ref_cod_matricula', 'ref_cod_turma', 'sequencial']);
        });

        Schema::table('pmieducar.matricula_excessao', function (Blueprint $table) {
            $table->foreign(
                ['ref_cod_matricula', 'ref_cod_turma', 'ref_sequencial'],
                'matricula_excessao_ref_cod_matricula_fkey'
            )
                ->references(['ref_cod_matricula', 'ref_cod_turma', 'sequencial'])
                ->on('pmieducar.matricula_turma');
        });
    }
}
