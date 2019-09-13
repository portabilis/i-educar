<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicMunicipioTable extends Migration
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
                
                CREATE SEQUENCE public.seq_municipio
                    START WITH 5565
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.municipio (
                    idmun numeric(6,0) DEFAULT nextval((\'public.seq_municipio\'::text)::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    sigla_uf character varying(3) NOT NULL,
                    area_km2 numeric(6,0),
                    idmreg numeric(2,0),
                    idasmun numeric(2,0),
                    cod_ibge numeric(20,0),
                    geom character varying,
                    tipo character(1) NOT NULL,
                    idmun_pai numeric(6,0),
                    idpes_rev numeric,
                    idpes_cad numeric,
                    data_rev timestamp without time zone,
                    data_cad timestamp without time zone NOT NULL,
                    origem_gravacao character(1) NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_municipio_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_municipio_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_municipio_tipo CHECK (((tipo = \'D\'::bpchar) OR (tipo = \'M\'::bpchar) OR (tipo = \'P\'::bpchar) OR (tipo = \'R\'::bpchar)))
                );
                
                ALTER TABLE ONLY public.municipio
                    ADD CONSTRAINT pk_municipio PRIMARY KEY (idmun);

                SELECT pg_catalog.setval(\'public.seq_municipio\', 5565, false);
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
        Schema::dropIfExists('public.municipio');

        DB::unprepared('DROP SEQUENCE public.seq_municipio;');
    }
}
