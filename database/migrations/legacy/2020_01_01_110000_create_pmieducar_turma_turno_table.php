<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarTurmaTurnoTable extends Migration
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
                CREATE SEQUENCE pmieducar.turma_turno_id_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.turma_turno (
                    id integer DEFAULT nextval(\'pmieducar.turma_turno_id_seq\'::regclass) NOT NULL,
                    nome character varying(15) NOT NULL,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );

                ALTER TABLE ONLY pmieducar.turma_turno
                    ADD CONSTRAINT turma_turno_pkey PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'pmieducar.turma_turno_id_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.turma_turno');

        DB::unprepared('DROP SEQUENCE pmieducar.turma_turno_id_seq;');
    }
}
