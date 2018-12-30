<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalNotPortalTipoTable extends Migration
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

                CREATE TABLE portal.not_portal_tipo (
                    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
                    ref_cod_not_tipo integer DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY portal.not_portal_tipo
                    ADD CONSTRAINT not_portal_tipo_pk PRIMARY KEY (ref_cod_not_portal, ref_cod_not_tipo);
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
        Schema::dropIfExists('portal.not_portal_tipo');
    }
}
