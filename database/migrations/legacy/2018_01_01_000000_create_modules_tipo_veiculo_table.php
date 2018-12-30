<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTipoVeiculoTable extends Migration
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

                CREATE SEQUENCE modules.tipo_veiculo_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.tipo_veiculo (
                    cod_tipo_veiculo integer DEFAULT nextval(\'modules.tipo_veiculo_seq\'::regclass) NOT NULL,
                    descricao character varying(60)
                );
                
                SELECT pg_catalog.setval(\'modules.tipo_veiculo_seq\', 1, false);
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
        Schema::dropIfExists('modules.tipo_veiculo');
    }
}
