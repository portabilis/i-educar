<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasFinalPregaoTable extends Migration
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

                CREATE TABLE portal.compras_final_pregao (
                    cod_compras_final_pregao integer DEFAULT nextval(\'portal.compras_final_pregao_cod_compras_final_pregao_seq\'::regclass) NOT NULL,
                    nm_final character varying(255) DEFAULT \'\'::character varying NOT NULL
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
        Schema::dropIfExists('portal.compras_final_pregao');
    }
}
