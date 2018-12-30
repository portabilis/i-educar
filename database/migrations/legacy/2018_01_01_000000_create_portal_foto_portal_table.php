<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalFotoPortalTable extends Migration
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

                CREATE SEQUENCE portal.foto_portal_cod_foto_portal_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.foto_portal (
                    cod_foto_portal integer DEFAULT nextval(\'portal.foto_portal_cod_foto_portal_seq\'::regclass) NOT NULL,
                    ref_cod_foto_secao integer,
                    ref_cod_credito integer,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    data_foto timestamp without time zone,
                    titulo character varying(255),
                    descricao text,
                    caminho character varying(255),
                    altura integer,
                    largura integer,
                    nm_credito character varying(255),
                    bkp_ref_secao bigint
                );
                
                ALTER TABLE ONLY portal.foto_portal
                    ADD CONSTRAINT foto_portal_pk PRIMARY KEY (cod_foto_portal);

                SELECT pg_catalog.setval(\'portal.foto_portal_cod_foto_portal_seq\', 1, false);
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
        Schema::dropIfExists('portal.foto_portal');

        DB::unprepared('DROP SEQUENCE portal.foto_portal_cod_foto_portal_seq;');
    }
}
