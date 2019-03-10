<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SituacaoMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                CREATE TABLE IF NOT EXISTS relatorio.situacao_matricula (
                    cod_situacao integer NOT NULL,
                    descricao character varying(50) NOT NULL,
                    PRIMARY KEY(cod_situacao)
                );
            '
        );

        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 1], ['descricao' => 'Aprovado']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 2], ['descricao' => 'Reprovado']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 15], ['descricao' => 'Falecido']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 4], ['descricao' => 'Transferido']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 6], ['descricao' => 'Abandono']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 13], ['descricao' => 'Aprovado pelo conselho']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 9], ['descricao' => 'Exceto Transferidos/Abandono']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 10], ['descricao' => 'Todas']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 14], ['descricao' => 'Reprovado por faltas']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 12], ['descricao' => 'Ap. Depen.']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 5], ['descricao' => 'Reclassificado']);
        DB::table('relatorio.situacao_matricula')->updateOrInsert(['cod_situacao' => 3], ['descricao' => 'Cursando']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TABLE relatorio.situacao_matricula CASCADE;');
    }
}
