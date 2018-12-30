<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalFotoSecaoTable extends Migration
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

                CREATE TABLE portal.foto_secao (
                    cod_foto_secao integer DEFAULT nextval(\'portal.foto_secao_cod_foto_secao_seq\'::regclass) NOT NULL,
                    nm_secao character varying(255)
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
        Schema::dropIfExists('portal.foto_secao');
    }
}
