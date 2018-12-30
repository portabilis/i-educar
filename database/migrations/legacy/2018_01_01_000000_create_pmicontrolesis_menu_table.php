<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisMenuTable extends Migration
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
                
                CREATE SEQUENCE pmicontrolesis.menu_cod_menu_seq
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmicontrolesis.menu (
                    cod_menu integer DEFAULT nextval(\'pmicontrolesis.menu_cod_menu_seq\'::regclass) NOT NULL,
                    ref_cod_menu_submenu integer,
                    ref_cod_menu_pai integer,
                    tt_menu character varying(255) NOT NULL,
                    ord_menu integer NOT NULL,
                    caminho character varying(255),
                    alvo character varying(20),
                    suprime_menu smallint DEFAULT 1,
                    ref_cod_tutormenu integer,
                    ref_cod_ico integer,
                    tipo_menu integer
                );
                
                ALTER TABLE ONLY pmicontrolesis.menu
                    ADD CONSTRAINT menu_pkey PRIMARY KEY (cod_menu);

                SELECT pg_catalog.setval(\'pmicontrolesis.menu_cod_menu_seq\', 20709, true);
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
        Schema::dropIfExists('pmicontrolesis.menu');

        DB::unprepared('DROP SEQUENCE pmicontrolesis.menu_cod_menu_seq;');
    }
}
