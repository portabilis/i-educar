<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarFaltaAtrasoCompensadoTable extends Migration
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
                CREATE SEQUENCE pmieducar.falta_atraso_compensado_cod_compensado_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.falta_atraso_compensado (
                    cod_compensado integer DEFAULT nextval(\'pmieducar.falta_atraso_compensado_cod_compensado_seq\'::regclass) NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    data_inicio timestamp without time zone NOT NULL,
                    data_fim timestamp without time zone NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.falta_atraso_compensado
                    ADD CONSTRAINT falta_atraso_compensado_pkey PRIMARY KEY (cod_compensado);

                SELECT pg_catalog.setval(\'pmieducar.falta_atraso_compensado_cod_compensado_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.falta_atraso_compensado');

        DB::unprepared('DROP SEQUENCE pmieducar.falta_atraso_compensado_cod_compensado_seq;');
    }
}
