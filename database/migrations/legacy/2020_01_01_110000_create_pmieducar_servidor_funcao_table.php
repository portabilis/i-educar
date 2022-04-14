<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorFuncaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.servidor_funcao_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.servidor_funcao (
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_cod_servidor integer NOT NULL,
                    ref_cod_funcao integer NOT NULL,
                    matricula character varying,
                    cod_servidor_funcao integer DEFAULT nextval(\'pmieducar.servidor_funcao_seq\'::regclass) NOT NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_funcao
                    ADD CONSTRAINT cod_servidor_funcao_pkey PRIMARY KEY (cod_servidor_funcao);

                SELECT pg_catalog.setval(\'pmieducar.servidor_funcao_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.servidor_funcao');

        DB::unprepared('DROP SEQUENCE pmieducar.servidor_funcao_seq;');
    }
}
