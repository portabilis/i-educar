<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesAcaoGovernoArquivoTable extends Migration
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
                SET default_with_oids = true;
                
                CREATE SEQUENCE pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmiacoes.acao_governo_arquivo (
                    cod_acao_governo_arquivo integer DEFAULT nextval(\'pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_cod_acao_governo integer NOT NULL,
                    nm_arquivo character varying(255) NOT NULL,
                    caminho_arquivo character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmiacoes.acao_governo_arquivo_cod_acao_governo_arquivo_seq\', 1, false);
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
        Schema::dropIfExists('pmiacoes.acao_governo_arquivo');
    }
}
