<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingEmailConteudoTable extends Migration
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

                CREATE SEQUENCE portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.mailling_email_conteudo (
                    cod_mailling_email_conteudo integer DEFAULT nextval(\'portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    conteudo text NOT NULL,
                    nm_remetente character varying(255),
                    email_remetente character varying(255),
                    assunto character varying(255)
                );
                
                SELECT pg_catalog.setval(\'portal.mailling_email_conteudo_cod_mailling_email_conteudo_seq\', 1, false);
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
        Schema::dropIfExists('portal.mailling_email_conteudo');
    }
}
