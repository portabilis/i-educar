<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE SEQUENCE modules.rota_transporte_escolar_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

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

                ALTER TABLE ONLY modules.rota_transporte_escolar
                    ADD CONSTRAINT rota_transporte_escolar_cod_rota_transporte_escolar_pkey PRIMARY KEY (cod_rota_transporte_escolar);

                SELECT pg_catalog.setval(\'modules.rota_transporte_escolar_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE modules.rota_transporte_escolar_seq;');
    }
}
