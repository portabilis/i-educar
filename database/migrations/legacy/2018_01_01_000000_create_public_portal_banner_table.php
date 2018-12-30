<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicPortalBannerTable extends Migration
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
                
                CREATE SEQUENCE public.portal_banner_cod_portal_banner_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.portal_banner (
                    cod_portal_banner integer DEFAULT nextval(\'public.portal_banner_cod_portal_banner_seq\'::regclass) NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    caminho character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    title character varying(255),
                    prioridade integer DEFAULT 0 NOT NULL,
                    link character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    lateral_ smallint DEFAULT (1)::smallint NOT NULL
                );
                
                SELECT pg_catalog.setval(\'public.portal_banner_cod_portal_banner_seq\', 1, false);
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
        Schema::dropIfExists('public.portal_banner');

        DB::unprepared('DROP SEQUENCE public.portal_banner_cod_portal_banner_seq;');
    }
}
