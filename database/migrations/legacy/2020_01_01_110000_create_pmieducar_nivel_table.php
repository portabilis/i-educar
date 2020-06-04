<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarNivelTable extends Migration
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
                SET default_with_oids = false;

                CREATE SEQUENCE pmieducar.nivel_cod_nivel_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.nivel (
                    cod_nivel integer DEFAULT nextval(\'pmieducar.nivel_cod_nivel_seq\'::regclass) NOT NULL,
                    ref_cod_categoria_nivel integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    ref_cod_nivel_anterior integer,
                    nm_nivel character varying(100) NOT NULL,
                    salario_base double precision,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT true NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.nivel
                    ADD CONSTRAINT nivel_pkey PRIMARY KEY (cod_nivel);

                SELECT pg_catalog.setval(\'pmieducar.nivel_cod_nivel_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.nivel');

        DB::unprepared('DROP SEQUENCE pmieducar.nivel_cod_nivel_seq;');
    }
}
