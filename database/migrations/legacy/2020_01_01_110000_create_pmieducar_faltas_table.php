<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarFaltasTable extends Migration
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
                CREATE SEQUENCE pmieducar.faltas_sequencial_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.faltas (
                    ref_cod_matricula integer NOT NULL,
                    sequencial integer DEFAULT nextval(\'pmieducar.faltas_sequencial_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    falta integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL
                );

                ALTER TABLE ONLY pmieducar.faltas
                    ADD CONSTRAINT faltas_pkey PRIMARY KEY (ref_cod_matricula, sequencial);

                SELECT pg_catalog.setval(\'pmieducar.faltas_sequencial_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.faltas');

        DB::unprepared('DROP SEQUENCE pmieducar.faltas_sequencial_seq;');
    }
}
