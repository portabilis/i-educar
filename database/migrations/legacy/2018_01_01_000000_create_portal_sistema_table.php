<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalSistemaTable extends Migration
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

                CREATE TABLE portal.sistema (
                    cod_sistema integer DEFAULT nextval(\'portal.sistema_cod_sistema_seq\'::regclass) NOT NULL,
                    nome character varying(255),
                    versao smallint NOT NULL,
                    release smallint NOT NULL,
                    patch smallint NOT NULL,
                    tipo character varying(255)
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
        Schema::dropIfExists('portal.sistema');
    }
}
