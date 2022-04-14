<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesVeiculoTable extends Migration
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
                CREATE SEQUENCE modules.veiculo_seq
                    START WITH 1
                    INCREMENT BY 1
                    NO MINVALUE
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE modules.veiculo (
                    cod_veiculo integer DEFAULT nextval(\'modules.veiculo_seq\'::regclass) NOT NULL,
                    descricao character varying(255) NOT NULL,
                    placa character varying(10),
                    renavam character varying(15) NOT NULL,
                    chassi character varying(30),
                    marca character varying(50),
                    ano_fabricacao integer,
                    ano_modelo integer,
                    passageiros integer NOT NULL,
                    malha character(1) NOT NULL,
                    ref_cod_tipo_veiculo integer NOT NULL,
                    exclusivo_transporte_escolar character(1) NOT NULL,
                    adaptado_necessidades_especiais character(1) NOT NULL,
                    ativo character(1),
                    descricao_inativo character(155),
                    ref_cod_empresa_transporte_escolar integer NOT NULL,
                    ref_cod_motorista integer,
                    observacao character varying(255)
                );

                ALTER TABLE ONLY modules.veiculo
                    ADD CONSTRAINT veiculo_pkey PRIMARY KEY (cod_veiculo);

                SELECT pg_catalog.setval(\'modules.veiculo_seq\', 1, false);
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
        Schema::dropIfExists('modules.veiculo');

        DB::unprepared('DROP SEQUENCE modules.veiculo_seq;');
    }
}
