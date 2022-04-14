<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE SEQUENCE modules.ponto_transporte_escolar_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

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

                ALTER TABLE ONLY modules.ponto_transporte_escolar
                    ADD CONSTRAINT ponto_transporte_escolar_cod_ponto_transporte_escolar_pkey PRIMARY KEY (cod_ponto_transporte_escolar);

                SELECT pg_catalog.setval(\'modules.ponto_transporte_escolar_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE modules.ponto_transporte_escolar_seq;');
    }
}
