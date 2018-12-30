<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmicontrolesisMenuPortalTable extends Migration
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
                
                CREATE TABLE pmicontrolesis.menu_portal (
                    cod_menu_portal integer DEFAULT nextval(\'pmicontrolesis.menu_portal_cod_menu_portal_seq\'::regclass) NOT NULL,
                    ref_funcionario_cad integer NOT NULL,
                    ref_funcionario_exc integer,
                    nm_menu character varying(15) DEFAULT \'\'::character varying NOT NULL,
                    title character varying(255),
                    caminho character varying(255),
                    cor character varying(255),
                    posicao character(1) DEFAULT \'E\'::bpchar NOT NULL,
                    ordem double precision DEFAULT (0)::double precision NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    expansivel smallint
                );
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
        Schema::dropIfExists('pmicontrolesis.menu_portal');
    }
}
