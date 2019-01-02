<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarExemplarTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.exemplar_cod_exemplar_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.exemplar (
                    cod_exemplar integer DEFAULT nextval(\'pmieducar.exemplar_cod_exemplar_seq\'::regclass) NOT NULL,
                    ref_cod_fonte integer NOT NULL,
                    ref_cod_motivo_baixa integer,
                    ref_cod_acervo integer NOT NULL,
                    ref_cod_situacao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    permite_emprestimo smallint DEFAULT (1)::smallint NOT NULL,
                    preco double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    data_aquisicao timestamp without time zone,
                    tombo integer,
                    sequencial integer,
                    data_baixa_exemplar date
                );
                
                ALTER TABLE ONLY pmieducar.exemplar
                    ADD CONSTRAINT exemplar_pkey PRIMARY KEY (cod_exemplar);

                CREATE INDEX exemplar_tombo_idx ON pmieducar.exemplar USING btree (tombo);

                SELECT pg_catalog.setval(\'pmieducar.exemplar_cod_exemplar_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.exemplar');

        DB::unprepared('DROP SEQUENCE pmieducar.exemplar_cod_exemplar_seq;');
    }
}
