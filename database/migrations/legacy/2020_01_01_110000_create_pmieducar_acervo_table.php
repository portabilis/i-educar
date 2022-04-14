<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAcervoTable extends Migration
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
                CREATE SEQUENCE pmieducar.acervo_cod_acervo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo (
                    cod_acervo integer DEFAULT nextval(\'pmieducar.acervo_cod_acervo_seq\'::regclass) NOT NULL,
                    ref_cod_exemplar_tipo integer NOT NULL,
                    ref_cod_acervo integer,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_acervo_colecao integer,
                    ref_cod_acervo_idioma integer NOT NULL,
                    ref_cod_acervo_editora integer NOT NULL,
                    titulo character varying(255) NOT NULL,
                    sub_titulo character varying(255),
                    cdu character varying(20),
                    cutter character varying(20),
                    volume integer,
                    num_edicao integer,
                    ano character varying(25),
                    num_paginas integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer NOT NULL,
                    isbn character varying(255),
                    cdd character varying(20),
                    estante character varying(20),
                    dimencao character varying(255),
                    material_ilustrativo character varying(255),
                    dimencao_ilustrativo character varying(255),
                    local character varying(255),
                    ref_cod_tipo_autor integer,
                    tipo_autor character varying(255)
                );

                ALTER TABLE ONLY pmieducar.acervo
                    ADD CONSTRAINT acervo_pkey PRIMARY KEY (cod_acervo);

                SELECT pg_catalog.setval(\'pmieducar.acervo_cod_acervo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_cod_acervo_seq;');
    }
}
