<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesAcaoGovernoFotoTable extends Migration
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
                
                CREATE SEQUENCE pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmiacoes.acao_governo_foto (
                    cod_acao_governo_foto integer DEFAULT nextval(\'pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_cod_acao_governo integer NOT NULL,
                    nm_foto character varying(255) NOT NULL,
                    caminho character varying(255) NOT NULL,
                    data_foto timestamp without time zone,
                    data_cadastro timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY pmiacoes.acao_governo_foto
                    ADD CONSTRAINT acao_governo_foto_pkey PRIMARY KEY (cod_acao_governo_foto);

                SELECT pg_catalog.setval(\'pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq\', 1, false);
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
        Schema::dropIfExists('pmiacoes.acao_governo_foto');

        DB::unprepared('DROP SEQUENCE pmiacoes.acao_governo_foto_cod_acao_governo_foto_seq;');
    }
}
