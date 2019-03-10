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
                    descricao character varying(50) NOT NULL
                );
            '
        );

        DB::unprepared(
            '
                ALTER TABLE ONLY relatorio.situacao_matricula
                    ADD CONSTRAINT situacao_matricula_pkey PRIMARY KEY (cod_situacao);
            '
        );

        DB::unprepared(
            '
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (1, \'Aprovado\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (2, \'Reprovado\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (15, \'Falecido\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (4, \'Transferido\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (6, \'Abandono\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (13, \'Aprovado pelo conselho\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (9, \'Exceto Transferidos/Abandono\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (10, \'Todas\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (14, \'Reprovado por faltas\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (12, \'Ap. Depen.\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (5, \'Reclassificado\');
                INSERT INTO relatorio.situacao_matricula (cod_situacao, descricao) VALUES (3, \'Cursando\');
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE relatorio.situacao_matricula DROP CONSTRAINT situacao_matricula_pkey;');
        DB::unprepared('DROP TABLE relatorio.situacao_matricula CASCADE;');
    }
}
