<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarServidorCursoTable extends Migration
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

                CREATE SEQUENCE pmieducar.servidor_curso_cod_servidor_curso_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.servidor_curso (
                    cod_servidor_curso integer DEFAULT nextval(\'pmieducar.servidor_curso_cod_servidor_curso_seq\'::regclass) NOT NULL,
                    ref_cod_formacao integer NOT NULL,
                    data_conclusao timestamp without time zone NOT NULL,
                    data_registro timestamp without time zone,
                    diplomas_registros text
                );
                
                ALTER TABLE ONLY pmieducar.servidor_curso
                    ADD CONSTRAINT servidor_curso_pkey PRIMARY KEY (cod_servidor_curso);

                SELECT pg_catalog.setval(\'pmieducar.servidor_curso_cod_servidor_curso_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.servidor_curso');

        DB::unprepared('DROP SEQUENCE pmieducar.servidor_curso_cod_servidor_curso_seq;');
    }
}
