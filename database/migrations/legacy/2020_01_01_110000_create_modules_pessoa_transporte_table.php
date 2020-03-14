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
                
                CREATE SEQUENCE modules.pessoa_transporte_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.pessoa_transporte (
                    cod_pessoa_transporte integer DEFAULT nextval(\'modules.pessoa_transporte_seq\'::regclass) NOT NULL,
                    ref_idpes integer NOT NULL,
                    ref_cod_rota_transporte_escolar integer NOT NULL,
                    ref_cod_ponto_transporte_escolar integer,
                    ref_idpes_destino integer,
                    observacao character varying(255),
                    turno character varying(255)
                );
                
                ALTER TABLE ONLY modules.pessoa_transporte
                    ADD CONSTRAINT pessoa_transporte_cod_pessoa_transporte_pkey PRIMARY KEY (cod_pessoa_transporte);

                SELECT pg_catalog.setval(\'modules.pessoa_transporte_seq\', 1, false);
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

        DB::unprepared('DROP SEQUENCE modules.pessoa_transporte_seq;');
    }
}
