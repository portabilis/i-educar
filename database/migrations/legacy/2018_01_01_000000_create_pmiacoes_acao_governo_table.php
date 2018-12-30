<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiacoesAcaoGovernoTable extends Migration
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
                
                CREATE SEQUENCE pmiacoes.acao_governo_cod_acao_governo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmiacoes.acao_governo (
                    cod_acao_governo integer DEFAULT nextval(\'pmiacoes.acao_governo_cod_acao_governo_seq\'::regclass) NOT NULL,
                    ref_funcionario_exc integer,
                    ref_funcionario_cad integer NOT NULL,
                    nm_acao character varying(255) NOT NULL,
                    descricao text,
                    data_inauguracao timestamp without time zone,
                    valor double precision,
                    destaque smallint DEFAULT (0)::smallint NOT NULL,
                    status_acao smallint DEFAULT (0)::smallint NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    numero_acao smallint DEFAULT 0,
                    categoria smallint,
                    idbai bigint
                );
                
                ALTER TABLE ONLY pmiacoes.acao_governo
                    ADD CONSTRAINT acao_governo_pkey PRIMARY KEY (cod_acao_governo);

                SELECT pg_catalog.setval(\'pmiacoes.acao_governo_cod_acao_governo_seq\', 1, false);
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
        Schema::dropIfExists('pmiacoes.acao_governo');

        DB::unprepared('DROP SEQUENCE pmiacoes.acao_governo_cod_acao_governo_seq;');
    }
}
