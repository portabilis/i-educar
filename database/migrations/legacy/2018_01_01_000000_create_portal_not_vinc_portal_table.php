<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalNotVincPortalTable extends Migration
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

                CREATE TABLE portal.not_vinc_portal (
                    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
                    vic_num integer DEFAULT 0 NOT NULL,
                    tipo character(1) DEFAULT \'F\'::bpchar NOT NULL,
                    cod_vinc integer,
                    caminho character varying(255),
                    nome_arquivo character varying(255)
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
        Schema::dropIfExists('portal.not_vinc_portal');
    }
}
