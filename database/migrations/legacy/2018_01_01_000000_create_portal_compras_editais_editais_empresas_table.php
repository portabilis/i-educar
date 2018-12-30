<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalComprasEditaisEditaisEmpresasTable extends Migration
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

                CREATE TABLE portal.compras_editais_editais_empresas (
                    ref_cod_compras_editais_editais integer DEFAULT 0 NOT NULL,
                    ref_cod_compras_editais_empresa integer DEFAULT 0 NOT NULL,
                    data_hora timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY portal.compras_editais_editais_empresas
                    ADD CONSTRAINT compras_editais_editais_empresas_pk PRIMARY KEY (ref_cod_compras_editais_editais, ref_cod_compras_editais_empresa, data_hora);
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
        Schema::dropIfExists('portal.compras_editais_editais_empresas');
    }
}
