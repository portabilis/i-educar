<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicDistritoTable extends Migration
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
                
                CREATE SEQUENCE public.seq_distrito
                    START WITH 0
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.distrito (
                    idmun numeric(6,0) NOT NULL,
                    geom character varying,
                    iddis numeric(6,0) DEFAULT nextval((\'public.seq_distrito\'::text)::regclass) NOT NULL,
                    nome character varying(80) NOT NULL,
                    cod_ibge character varying(7),
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    CONSTRAINT ck_distrito_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_distrito_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar)))
                );
                
                ALTER TABLE ONLY public.distrito
                    ADD CONSTRAINT pk_distrito PRIMARY KEY (iddis);

                SELECT pg_catalog.setval(\'public.seq_distrito\', 10839, true);
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
        Schema::dropIfExists('public.distrito');

        DB::unprepared('DROP SEQUENCE public.seq_distrito;');
    }
}
