<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesPontoTransporteEscolarTable extends Migration
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

                CREATE TABLE modules.ponto_transporte_escolar (
                    cod_ponto_transporte_escolar integer DEFAULT nextval(\'modules.ponto_transporte_escolar_seq\'::regclass) NOT NULL,
                    descricao character varying(70) NOT NULL,
                    cep numeric(8,0),
                    idlog numeric(6,0),
                    idbai numeric(6,0),
                    numero numeric(6,0),
                    complemento character varying(20),
                    latitude character varying(20),
                    longitude character varying(20)
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
        Schema::dropIfExists('modules.ponto_transporte_escolar');
    }
}
