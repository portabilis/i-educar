<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalNotTipoTable extends Migration
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

                CREATE TABLE portal.not_tipo (
                    cod_not_tipo integer DEFAULT nextval(\'portal.not_tipo_cod_not_tipo_seq\'::regclass) NOT NULL,
                    nm_tipo character varying(255) DEFAULT \'\'::character varying NOT NULL
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
        Schema::dropIfExists('portal.not_tipo');
    }
}
