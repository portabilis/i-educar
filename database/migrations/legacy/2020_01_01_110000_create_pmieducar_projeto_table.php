<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarProjetoTable extends Migration
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
                CREATE SEQUENCE pmieducar.projeto_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.projeto (
                    cod_projeto integer DEFAULT nextval(\'pmieducar.projeto_seq\'::regclass) NOT NULL,
                    nome character varying(50),
                    observacao character varying(255)
                );

                ALTER TABLE ONLY pmieducar.projeto
                    ADD CONSTRAINT pmieducar_projeto_cod_projeto PRIMARY KEY (cod_projeto);

                SELECT pg_catalog.setval(\'pmieducar.projeto_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.projeto');

        DB::unprepared('DROP SEQUENCE pmieducar.projeto_seq;');
    }
}
