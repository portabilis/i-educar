<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisSubmenuPortalTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.submenu_portal_cod_submenu_portal_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.submenu_portal (
                    cod_submenu_portal integer DEFAULT nextval(\'pmicontrolesis.submenu_portal_cod_submenu_portal_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    ref_cod_menu_portal integer DEFAULT 0 NOT NULL,
                    nm_submenu character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    arquivo character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    target character(1) DEFAULT \'S\'::bpchar NOT NULL,
                    title text,
                    ordem double precision DEFAULT (0)::double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmicontrolesis.submenu_portal_cod_submenu_portal_seq\', 1, false);
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
        Schema::dropIfExists('pmicontrolesis.submenu_portal');
    }
}
