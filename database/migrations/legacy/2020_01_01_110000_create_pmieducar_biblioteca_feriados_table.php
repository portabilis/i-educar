<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarBibliotecaFeriadosTable extends Migration
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
                CREATE SEQUENCE pmieducar.biblioteca_feriados_cod_feriado_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.biblioteca_feriados (
                    cod_feriado integer DEFAULT nextval(\'pmieducar.biblioteca_feriados_cod_feriado_seq\'::regclass) NOT NULL,
                    ref_cod_biblioteca integer NOT NULL,
                    nm_feriado character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    data_feriado date NOT NULL
                );

                ALTER TABLE ONLY pmieducar.biblioteca_feriados
                    ADD CONSTRAINT biblioteca_feriados_pkey PRIMARY KEY (cod_feriado);

                SELECT pg_catalog.setval(\'pmieducar.biblioteca_feriados_cod_feriado_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.biblioteca_feriados');

        DB::unprepared('DROP SEQUENCE pmieducar.biblioteca_feriados_cod_feriado_seq;');
    }
}
