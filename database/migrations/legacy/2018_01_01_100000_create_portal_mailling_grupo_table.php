<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingGrupoTable extends Migration
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

                CREATE SEQUENCE portal.mailling_grupo_cod_mailling_grupo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE portal.mailling_grupo (
                    cod_mailling_grupo integer DEFAULT nextval(\'portal.mailling_grupo_cod_mailling_grupo_seq\'::regclass) NOT NULL,
                    nm_grupo character varying(255) DEFAULT \'\'::character varying NOT NULL
                );
                
                ALTER TABLE ONLY portal.mailling_grupo
                    ADD CONSTRAINT mailling_grupo_pk PRIMARY KEY (cod_mailling_grupo);

                SELECT pg_catalog.setval(\'portal.mailling_grupo_cod_mailling_grupo_seq\', 1, false);
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
        Schema::dropIfExists('portal.mailling_grupo');

        DB::unprepared('DROP SEQUENCE portal.mailling_grupo_cod_mailling_grupo_seq;');
    }
}
