<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesMotoristaTable extends Migration
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

                CREATE TABLE modules.motorista (
                    cod_motorista integer DEFAULT nextval(\'modules.motorista_seq\'::regclass) NOT NULL,
                    ref_idpes integer NOT NULL,
                    cnh character varying(15),
                    tipo_cnh character varying(2),
                    dt_habilitacao date,
                    vencimento_cnh date,
                    ref_cod_empresa_transporte_escolar integer NOT NULL,
                    observacao character varying(255)
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
        Schema::dropIfExists('modules.motorista');
    }
}
