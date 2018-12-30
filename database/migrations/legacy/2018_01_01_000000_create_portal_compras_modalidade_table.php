<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasModalidadeTable extends Migration
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

                CREATE TABLE portal.compras_modalidade (
                    cod_compras_modalidade integer DEFAULT nextval(\'portal.compras_modalidade_cod_compras_modalidade_seq\'::regclass) NOT NULL,
                    nm_modalidade character varying(255) DEFAULT \'\'::character varying NOT NULL
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
        Schema::dropIfExists('portal.compras_modalidade');
    }
}
