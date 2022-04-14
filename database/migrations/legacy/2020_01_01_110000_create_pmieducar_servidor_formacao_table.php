<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorFormacaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.servidor_formacao_cod_formacao_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.servidor_formacao (
                    cod_formacao integer DEFAULT nextval(\'pmieducar.servidor_formacao_cod_formacao_seq\'::regclass) NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    nm_formacao character varying(255) NOT NULL,
                    tipo character(1) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_formacao
                    ADD CONSTRAINT servidor_formacao_pkey PRIMARY KEY (cod_formacao);

                SELECT pg_catalog.setval(\'pmieducar.servidor_formacao_cod_formacao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.servidor_formacao');

        DB::unprepared('DROP SEQUENCE pmieducar.servidor_formacao_cod_formacao_seq;');
    }
}
