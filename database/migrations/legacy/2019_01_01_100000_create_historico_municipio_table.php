<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricoMunicipioTable extends Migration
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
                
                CREATE TABLE historico.municipio (
                    idmun numeric(6,0) NOT NULL,
                    nome character varying(60) NOT NULL,
                    sigla_uf character(2) NOT NULL,
                    area_km2 numeric(6,0),
                    idmreg numeric(2,0),
                    idasmun numeric(2,0),
                    cod_ibge numeric(20,0),
                    geom character varying,
                    tipo character(1) NOT NULL,
                    idmun_pai numeric(6,0),
                    idpes_rev numeric,
                    idsis_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    idsis_cad numeric NOT NULL,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_municipio_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_municipio_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_municipio_tipo CHECK (((tipo = \'D\'::bpchar) OR (tipo = \'M\'::bpchar) OR (tipo = \'P\'::bpchar) OR (tipo = \'R\'::bpchar)))
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
        Schema::dropIfExists('historico.municipio');
    }
}
