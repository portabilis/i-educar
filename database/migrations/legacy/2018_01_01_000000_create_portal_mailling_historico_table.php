<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingHistoricoTable extends Migration
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

                CREATE TABLE portal.mailling_historico (
                    cod_mailling_historico integer DEFAULT nextval(\'portal.mailling_historico_cod_mailling_historico_seq\'::regclass) NOT NULL,
                    ref_cod_not_portal integer DEFAULT 0 NOT NULL,
                    ref_cod_mailling_grupo integer DEFAULT 0 NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    data_hora timestamp without time zone NOT NULL
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
        Schema::dropIfExists('portal.mailling_historico');
    }
}
