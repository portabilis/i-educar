<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarAcervoAssuntoTable extends Migration
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
                
                CREATE SEQUENCE pmieducar.acervo_assunto_cod_acervo_assunto_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.acervo_assunto (
                    cod_acervo_assunto integer DEFAULT nextval(\'pmieducar.acervo_assunto_cod_acervo_assunto_seq\'::regclass) NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    nm_assunto character varying(255) NOT NULL,
                    descricao text,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    ref_cod_biblioteca integer
                );
                
                ALTER TABLE ONLY pmieducar.acervo_assunto
                    ADD CONSTRAINT acervo_assunto_pkey PRIMARY KEY (cod_acervo_assunto);

                SELECT pg_catalog.setval(\'pmieducar.acervo_assunto_cod_acervo_assunto_seq\', 1, false);
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
        Schema::dropIfExists('pmieducar.acervo_assunto');

        DB::unprepared('DROP SEQUENCE pmieducar.acervo_assunto_cod_acervo_assunto_seq;');
    }
}
