<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarNivelEnsinoTable extends Migration
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
                CREATE SEQUENCE pmieducar.nivel_ensino_cod_nivel_ensino_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.nivel_ensino (
                    cod_nivel_ensino integer DEFAULT nextval(\'pmieducar.nivel_ensino_cod_nivel_ensino_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_nivel character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_instituicao integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.nivel_ensino
                    ADD CONSTRAINT nivel_ensino_pkey PRIMARY KEY (cod_nivel_ensino);

                SELECT pg_catalog.setval(\'pmieducar.nivel_ensino_cod_nivel_ensino_seq\', 1, true);
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
        Schema::dropIfExists('pmieducar.nivel_ensino');

        DB::unprepared('DROP SEQUENCE pmieducar.nivel_ensino_cod_nivel_ensino_seq;');
    }
}
