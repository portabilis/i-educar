<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarOperadorTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.operador_cod_operador_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.operador (
                    cod_operador integer DEFAULT nextval(\'pmieducar.operador_cod_operador_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nome character varying(50) NOT NULL,
                    valor text NOT NULL,
                    fim_sentenca smallint DEFAULT (1)::smallint NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.operador
                    ADD CONSTRAINT operador_pkey PRIMARY KEY (cod_operador);

                SELECT pg_catalog.setval(\'pmieducar.operador_cod_operador_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.operador');

        DB::unprepared('DROP SEQUENCE pmieducar.operador_cod_operador_seq;');
    }
}
