<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoMetadadoTable extends Migration
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
                
                CREATE SEQUENCE consistenciacao.metadado_idmet_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE consistenciacao.metadado (
                    idmet integer DEFAULT nextval(\'consistenciacao.metadado_idmet_seq\'::regclass) NOT NULL,
                    idfon integer NOT NULL,
                    nome character varying(60) NOT NULL,
                    situacao character(1) NOT NULL,
                    separador character(1),
                    CONSTRAINT ck_metadado_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                ALTER TABLE ONLY consistenciacao.metadado
                    ADD CONSTRAINT pk_metadado PRIMARY KEY (idmet);

                SELECT pg_catalog.setval(\'consistenciacao.metadado_idmet_seq\', 1, false);
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
        Schema::dropIfExists('consistenciacao.metadado');

        DB::unprepared('DROP SEQUENCE consistenciacao.metadado_idmet_seq;');
    }
}
