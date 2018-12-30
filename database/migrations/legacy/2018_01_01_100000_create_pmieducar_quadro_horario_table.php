<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarQuadroHorarioTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.quadro_horario_cod_quadro_horario_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.quadro_horario (
                    cod_quadro_horario integer DEFAULT nextval(\'pmieducar.quadro_horario_cod_quadro_horario_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_turma integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ano integer
                );
                
                ALTER TABLE ONLY pmieducar.quadro_horario
                    ADD CONSTRAINT quadro_horario_pkey PRIMARY KEY (cod_quadro_horario);

                SELECT pg_catalog.setval(\'pmieducar.quadro_horario_cod_quadro_horario_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.quadro_horario');

        DB::unprepared('DROP SEQUENCE pmieducar.quadro_horario_cod_quadro_horario_seq;');
    }
}
