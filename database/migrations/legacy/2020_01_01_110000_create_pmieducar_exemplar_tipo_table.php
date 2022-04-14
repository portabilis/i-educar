<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarExemplarTipoTable extends Migration
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
                CREATE SEQUENCE pmieducar.exemplar_tipo_cod_exemplar_tipo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.exemplar_tipo (
                    cod_exemplar_tipo integer DEFAULT nextval(\'pmieducar.exemplar_tipo_cod_exemplar_tipo_seq\'::regclass) NOT NULL,
                    ref_cod_biblioteca integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.exemplar_tipo
                    ADD CONSTRAINT exemplar_tipo_pkey PRIMARY KEY (cod_exemplar_tipo);

                SELECT pg_catalog.setval(\'pmieducar.exemplar_tipo_cod_exemplar_tipo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.exemplar_tipo');

        DB::unprepared('DROP SEQUENCE pmieducar.exemplar_tipo_cod_exemplar_tipo_seq;');
    }
}
