<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAcervoIdiomaTable extends Migration
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
                CREATE SEQUENCE pmieducar.acervo_idioma_cod_acervo_idioma_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo_idioma (
                    cod_acervo_idioma integer DEFAULT nextval(\'pmieducar.acervo_idioma_cod_acervo_idioma_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_idioma character varying(255) NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );

                ALTER TABLE ONLY pmieducar.acervo_idioma
                    ADD CONSTRAINT acervo_idioma_pkey PRIMARY KEY (cod_acervo_idioma);

                SELECT pg_catalog.setval(\'pmieducar.acervo_idioma_cod_acervo_idioma_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo_idioma');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_idioma_cod_acervo_idioma_seq;');
    }
}
