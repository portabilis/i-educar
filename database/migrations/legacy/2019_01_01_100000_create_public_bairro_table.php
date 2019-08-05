<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePublicBairroTable extends Migration
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
                
                CREATE SEQUENCE public.seq_bairro
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE public.bairro (
                    idmun numeric(6,0) NOT NULL,
                    geom character varying,
                    idbai numeric(6,0) DEFAULT nextval((\'public.seq_bairro\'::text)::regclass) NOT NULL,
                    nome character varying(80) NOT NULL,
                    idpes_rev numeric,
                    data_rev timestamp without time zone,
                    origem_gravacao character(1) NOT NULL,
                    idpes_cad numeric,
                    data_cad timestamp without time zone NOT NULL,
                    operacao character(1) NOT NULL,
                    zona_localizacao integer DEFAULT 1,
                    iddis integer NOT NULL,
                    idsetorbai numeric(6,0),
                    CONSTRAINT ck_bairro_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_bairro_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar)))
                );
                
                ALTER TABLE ONLY public.bairro
                    ADD CONSTRAINT pk_bairro PRIMARY KEY (idbai);

                SELECT pg_catalog.setval(\'public.seq_bairro\', 1, false);
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
        Schema::dropIfExists('public.bairro');

        DB::unprepared('DROP SEQUENCE public.seq_bairro;');
    }
}
