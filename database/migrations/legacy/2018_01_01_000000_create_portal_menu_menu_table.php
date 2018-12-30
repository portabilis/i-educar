<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMenuMenuTable extends Migration
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

                CREATE SEQUENCE portal.menu_menu_cod_menu_menu_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.menu_menu (
                    cod_menu_menu integer DEFAULT nextval(\'portal.menu_menu_cod_menu_menu_seq\'::regclass) NOT NULL,
                    nm_menu character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    title character varying(255),
                    ref_cod_menu_pai integer,
                    caminho character varying(255) DEFAULT \'#\'::character varying,
                    ord_menu integer DEFAULT 9999,
                    ativo boolean DEFAULT true,
                    icon_class character varying(20)
                );
                
                SELECT pg_catalog.setval(\'portal.menu_menu_cod_menu_menu_seq\', 68, true);
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
        Schema::dropIfExists('portal.menu_menu');
    }
}
