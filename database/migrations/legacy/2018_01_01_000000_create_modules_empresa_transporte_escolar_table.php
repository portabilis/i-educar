<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesEmpresaTransporteEscolarTable extends Migration
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

                CREATE SEQUENCE modules.empresa_transporte_escolar_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.empresa_transporte_escolar (
                    cod_empresa_transporte_escolar integer DEFAULT nextval(\'modules.empresa_transporte_escolar_seq\'::regclass) NOT NULL,
                    ref_idpes integer NOT NULL,
                    ref_resp_idpes integer NOT NULL,
                    observacao character varying(255)
                );
                
                SELECT pg_catalog.setval(\'modules.empresa_transporte_escolar_seq\', 1, false);
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
        Schema::dropIfExists('modules.empresa_transporte_escolar');
    }
}
