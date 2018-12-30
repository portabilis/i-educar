<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarServidorTituloConcursoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.servidor_titulo_concurso (
                    cod_servidor_titulo integer DEFAULT nextval(\'pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq\'::regclass) NOT NULL,
                    ref_cod_formacao integer NOT NULL,
                    data_vigencia_homolog timestamp without time zone NOT NULL,
                    data_publicacao timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.servidor_titulo_concurso
                    ADD CONSTRAINT servidor_titulo_concurso_pkey PRIMARY KEY (cod_servidor_titulo);

                SELECT pg_catalog.setval(\'pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.servidor_titulo_concurso');

        DB::unprepared('DROP SEQUENCE pmieducar.servidor_titulo_concurso_cod_servidor_titulo_seq;');
    }
}
