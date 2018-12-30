<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAcervoColecaoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.acervo_colecao_cod_acervo_colecao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo_colecao (
                    cod_acervo_colecao integer DEFAULT nextval(\'pmieducar.acervo_colecao_cod_acervo_colecao_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_colecao character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );
                
                ALTER TABLE ONLY pmieducar.acervo_colecao
                    ADD CONSTRAINT acervo_colecao_pkey PRIMARY KEY (cod_acervo_colecao);

                SELECT pg_catalog.setval(\'pmieducar.acervo_colecao_cod_acervo_colecao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo_colecao');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_colecao_cod_acervo_colecao_seq;');
    }
}
