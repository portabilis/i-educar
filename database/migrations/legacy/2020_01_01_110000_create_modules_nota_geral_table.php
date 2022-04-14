<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesNotaGeralTable extends Migration
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
                CREATE SEQUENCE modules.nota_geral_id_seq
                    START WITH 958638
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.nota_geral (
                    id integer DEFAULT nextval(\'modules.nota_geral_id_seq\'::regclass) NOT NULL,
                    nota_aluno_id integer NOT NULL,
                    nota numeric(8,4) DEFAULT 0,
                    nota_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL
                );

                ALTER TABLE ONLY modules.nota_geral
                    ADD CONSTRAINT nota_geral_pkey PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'modules.nota_geral_id_seq\', 958638, false);
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
        Schema::dropIfExists('modules.nota_geral');

        DB::unprepared('DROP SEQUENCE modules.nota_geral_id_seq;');
    }
}
