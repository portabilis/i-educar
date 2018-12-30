<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasEditaisEditaisTable extends Migration
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

                CREATE TABLE portal.compras_editais_editais (
                    cod_compras_editais_editais integer DEFAULT nextval(\'portal.compras_editais_editais_cod_compras_editais_editais_seq\'::regclass) NOT NULL,
                    ref_cod_compras_licitacoes integer DEFAULT 0 NOT NULL,
                    versao integer DEFAULT 0 NOT NULL,
                    data_hora timestamp without time zone NOT NULL,
                    arquivo character varying(255) DEFAULT \'\'::character varying NOT NULL,
                    ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    motivo_alteracao text,
                    visivel smallint DEFAULT 1 NOT NULL
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
        Schema::dropIfExists('portal.compras_editais_editais');
    }
}
