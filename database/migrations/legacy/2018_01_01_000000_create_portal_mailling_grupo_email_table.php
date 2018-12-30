<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingGrupoEmailTable extends Migration
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

                CREATE TABLE portal.mailling_grupo_email (
                    ref_cod_mailling_email integer DEFAULT 0 NOT NULL,
                    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY portal.mailling_grupo_email
                    ADD CONSTRAINT mailling_grupo_email_pk PRIMARY KEY (ref_cod_mailling_email, ref_cod_mailling_grupo);
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
        Schema::dropIfExists('portal.mailling_grupo_email');
    }
}
