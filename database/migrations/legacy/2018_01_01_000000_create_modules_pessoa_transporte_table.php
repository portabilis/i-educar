<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesPessoaTransporteTable extends Migration
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
                
                CREATE TABLE modules.pessoa_transporte (
                    cod_pessoa_transporte integer DEFAULT nextval(\'modules.pessoa_transporte_seq\'::regclass) NOT NULL,
                    ref_idpes integer NOT NULL,
                    ref_cod_rota_transporte_escolar integer NOT NULL,
                    ref_cod_ponto_transporte_escolar integer,
                    ref_idpes_destino integer,
                    observacao character varying(255),
                    turno character varying(255)
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
        Schema::dropIfExists('modules.pessoa_transporte');
    }
}
