<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroEnderecoExternoTable extends Migration
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
                
                CREATE TABLE cadastro.endereco_externo (
                    idpes numeric(8,0) NOT NULL,
                    tipo numeric(1,0) NOT NULL,
                    idtlog character varying(5) NOT NULL,
                    logradouro character varying(150) NOT NULL,
                    numero numeric(10) NULL,
                    letra character(1),
                    complemento character varying(20),
                    bairro character varying(40),
                    cep numeric(8,0),
                    cidade character varying(60) NOT NULL,
                    sigla_uf character(2) NOT NULL,
                    reside_desde date,
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    bloco character varying(20),
                    andar numeric(2,0),
                    apartamento numeric(6,0),
                    zona_localizacao integer DEFAULT 1,
                    CONSTRAINT ck_endereco_externo_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_endereco_externo_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_endereco_externo_tipo CHECK (((tipo >= (1)::numeric) AND (tipo <= (3)::numeric)))
                );
                
                ALTER TABLE ONLY cadastro.endereco_externo
                    ADD CONSTRAINT pk_endereco_externo PRIMARY KEY (idpes, tipo);
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
        Schema::dropIfExists('cadastro.endereco_externo');
    }
}
