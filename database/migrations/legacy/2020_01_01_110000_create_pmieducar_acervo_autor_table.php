<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarAcervoAutorTable extends Migration
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
                CREATE SEQUENCE pmieducar.acervo_autor_cod_acervo_autor_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo_autor (
                    cod_acervo_autor integer DEFAULT nextval(\'pmieducar.acervo_autor_cod_acervo_autor_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_autor character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.acervo_autor
                    ADD CONSTRAINT acervo_autor_pkey PRIMARY KEY (cod_acervo_autor);

                SELECT pg_catalog.setval(\'pmieducar.acervo_autor_cod_acervo_autor_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo_autor');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_autor_cod_acervo_autor_seq;');
    }
}
