<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmidrhDiariaGrupoTable extends Migration
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
                
                CREATE SEQUENCE pmidrh.diaria_grupo_cod_diaria_grupo_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmidrh.diaria_grupo (
                    cod_diaria_grupo integer DEFAULT nextval(\'pmidrh.diaria_grupo_cod_diaria_grupo_seq\'::regclass) NOT NULL,
                    desc_grupo character varying(255) NOT NULL
                );
                
                SELECT pg_catalog.setval(\'pmidrh.diaria_grupo_cod_diaria_grupo_seq\', 1, false);
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
        Schema::dropIfExists('pmidrh.diaria_grupo');
    }
}
