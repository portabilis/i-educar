<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMenuSubmenuTable extends Migration
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

                CREATE SEQUENCE portal.menu_submenu_cod_menu_submenu_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.menu_submenu (
                    cod_menu_submenu integer DEFAULT nextval(\'portal.menu_submenu_cod_menu_submenu_seq\'::regclass) NOT NULL,
                    ref_cod_menu_menu integer,
                    cod_sistema integer,
                    nm_submenu character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    arquivo character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    title text,
                    nivel smallint DEFAULT (3)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY portal.menu_submenu
                    ADD CONSTRAINT menu_submenu_pk PRIMARY KEY (cod_menu_submenu);

                SELECT pg_catalog.setval(\'portal.menu_submenu_cod_menu_submenu_seq\', 944, true);
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
        Schema::dropIfExists('portal.menu_submenu');

        DB::unprepared('DROP SEQUENCE portal.menu_submenu_cod_menu_submenu_seq;');
    }
}
