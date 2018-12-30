<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesRotaTransporteEscolarTable extends Migration
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

                CREATE TABLE modules.rota_transporte_escolar (
                    cod_rota_transporte_escolar integer DEFAULT nextval(\'modules.rota_transporte_escolar_seq\'::regclass) NOT NULL,
                    ref_idpes_destino integer NOT NULL,
                    descricao character varying(50) NOT NULL,
                    ano integer NOT NULL,
                    tipo_rota character(1) NOT NULL,
                    km_pav double precision,
                    km_npav double precision,
                    ref_cod_empresa_transporte_escolar integer,
                    tercerizado character(1) NOT NULL
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
        Schema::dropIfExists('modules.rota_transporte_escolar');
    }
}
