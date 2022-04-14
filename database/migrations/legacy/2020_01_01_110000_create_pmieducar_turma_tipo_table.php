<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarTurmaTipoTable extends Migration
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
                CREATE SEQUENCE pmieducar.turma_tipo_cod_turma_tipo_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.turma_tipo (
                    cod_turma_tipo integer DEFAULT nextval(\'pmieducar.turma_tipo_cod_turma_tipo_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_tipo character varying(255) NOT NULL,
                    sgl_tipo character varying(15) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer
                );

                ALTER TABLE ONLY pmieducar.turma_tipo
                    ADD CONSTRAINT turma_tipo_pkey PRIMARY KEY (cod_turma_tipo);

                SELECT pg_catalog.setval(\'pmieducar.turma_tipo_cod_turma_tipo_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.turma_tipo');

        DB::unprepared('DROP SEQUENCE pmieducar.turma_tipo_cod_turma_tipo_seq;');
    }
}
