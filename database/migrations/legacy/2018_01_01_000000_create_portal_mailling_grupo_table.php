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

                CREATE TABLE portal.mailling_grupo (
                    cod_mailling_grupo integer DEFAULT nextval(\'portal.mailling_grupo_cod_mailling_grupo_seq\'::regclass) NOT NULL,
                    nm_grupo character varying(255) DEFAULT \'\'::character varying NOT NULL
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
        Schema::dropIfExists('portal.mailling_grupo');
    }
}
