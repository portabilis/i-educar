<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarCalendarioDiaMotivoTable extends Migration
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
                CREATE SEQUENCE pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.calendario_dia_motivo (
                    cod_calendario_dia_motivo integer DEFAULT nextval(\'pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq\'::regclass) NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    sigla character varying(15) NOT NULL,
                    descricao text,
                    tipo character(1) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    nm_motivo character varying(255) NOT NULL
                );

                ALTER TABLE ONLY pmieducar.calendario_dia_motivo
                    ADD CONSTRAINT calendario_dia_motivo_pkey PRIMARY KEY (cod_calendario_dia_motivo);

                CREATE INDEX i_calendario_dia_motivo_ativo ON pmieducar.calendario_dia_motivo USING btree (ativo);

                CREATE INDEX i_calendario_dia_motivo_ref_cod_escola ON pmieducar.calendario_dia_motivo USING btree (ref_cod_escola);

                CREATE INDEX i_calendario_dia_motivo_ref_usuario_cad ON pmieducar.calendario_dia_motivo USING btree (ref_usuario_cad);

                CREATE INDEX i_calendario_dia_motivo_sigla ON pmieducar.calendario_dia_motivo USING btree (sigla);

                CREATE INDEX i_calendario_dia_motivo_tipo ON pmieducar.calendario_dia_motivo USING btree (tipo);

                SELECT pg_catalog.setval(\'pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.calendario_dia_motivo');

        DB::unprepared('DROP SEQUENCE pmieducar.calendario_dia_motivo_cod_calendario_dia_motivo_seq;');
    }
}
