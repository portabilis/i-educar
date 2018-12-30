<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalMaillingFilaEnvioTable extends Migration
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

                CREATE TABLE portal.mailling_fila_envio (
                    cod_mailling_fila_envio integer DEFAULT nextval(\'portal.mailling_fila_envio_cod_mailling_fila_envio_seq\'::regclass) NOT NULL,
                    ref_cod_mailling_email_conteudo integer DEFAULT 0 NOT NULL,
                    ref_cod_mailling_email integer,
                    ref_ref_cod_pessoa_fj integer,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_envio timestamp without time zone
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
        Schema::dropIfExists('portal.mailling_fila_envio');
    }
}
