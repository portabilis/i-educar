<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarCategoriaNivelTable extends Migration
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
                CREATE SEQUENCE pmieducar.categoria_nivel_cod_categoria_nivel_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.categoria_nivel (
                    cod_categoria_nivel integer DEFAULT nextval(\'pmieducar.categoria_nivel_cod_categoria_nivel_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_categoria_nivel character varying(100) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo boolean DEFAULT true NOT NULL
                );

                ALTER TABLE ONLY pmieducar.categoria_nivel
                    ADD CONSTRAINT categoria_nivel_pkey PRIMARY KEY (cod_categoria_nivel);

                SELECT pg_catalog.setval(\'pmieducar.categoria_nivel_cod_categoria_nivel_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.categoria_nivel');

        DB::unprepared('DROP SEQUENCE pmieducar.categoria_nivel_cod_categoria_nivel_seq;');
    }
}
