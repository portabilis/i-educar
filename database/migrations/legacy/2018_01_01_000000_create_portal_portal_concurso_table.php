<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalPortalConcursoTable extends Migration
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

                CREATE SEQUENCE portal.portal_concurso_cod_portal_concurso_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.portal_concurso (
                    cod_portal_concurso integer DEFAULT nextval(\'portal.portal_concurso_cod_portal_concurso_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    nm_concurso character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    descricao text,
                    caminho character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    tipo_arquivo character(3) DEFAULT \'\'::bpchar NOT NULL,
                    data_hora timestamp without time zone
                );
                
                SELECT pg_catalog.setval(\'portal.portal_concurso_cod_portal_concurso_seq\', 1, false);
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
        Schema::dropIfExists('portal.portal_concurso');
    }
}
