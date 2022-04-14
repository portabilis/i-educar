<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarInstituicaoDocumentacaoTable extends Migration
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
                CREATE SEQUENCE pmieducar.instituicao_documentacao_seq
                    START WITH 2
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.instituicao_documentacao (
                    id integer DEFAULT nextval(\'pmieducar.instituicao_documentacao_seq\'::regclass) NOT NULL,
                    instituicao_id integer NOT NULL,
                    titulo_documento character varying(100) NOT NULL,
                    url_documento character varying(255) NOT NULL,
                    ref_usuario_cad integer DEFAULT 0 NOT NULL,
                    ref_cod_escola integer
                );

                ALTER TABLE ONLY pmieducar.instituicao_documentacao
                    ADD CONSTRAINT instituicao_documentacao_pkey PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'pmieducar.instituicao_documentacao_seq\', 2, false);
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
        Schema::dropIfExists('pmieducar.instituicao_documentacao');

        DB::unprepared('DROP SEQUENCE pmieducar.instituicao_documentacao_seq;');
    }
}
