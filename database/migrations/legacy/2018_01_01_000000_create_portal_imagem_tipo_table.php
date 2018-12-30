<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalImagemTipoTable extends Migration
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

                CREATE TABLE portal.imagem_tipo (
                    cod_imagem_tipo integer DEFAULT nextval(\'portal.imagem_tipo_cod_imagem_tipo_seq\'::regclass) NOT NULL,
                    nm_tipo character varying(100) NOT NULL
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
        Schema::dropIfExists('portal.imagem_tipo');
    }
}
