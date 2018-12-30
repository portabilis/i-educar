<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasPrestacaoContasTable extends Migration
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

                CREATE TABLE portal.compras_prestacao_contas (
                    cod_compras_prestacao_contas integer DEFAULT nextval(\'portal.compras_prestacao_contas_cod_compras_prestacao_contas_seq\'::regclass) NOT NULL,
                    caminho character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    mes integer DEFAULT 0 NOT NULL,
                    ano integer DEFAULT 0 NOT NULL
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
        Schema::dropIfExists('portal.compras_prestacao_contas');
    }
}
