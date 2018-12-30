<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoConfrontacaoTable extends Migration
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
                
                CREATE SEQUENCE consistenciacao.confrontacao_idcon_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE consistenciacao.confrontacao (
                    idcon integer DEFAULT nextval(\'consistenciacao.confrontacao_idcon_seq\'::regclass) NOT NULL,
                    idins integer NOT NULL,
                    idpes integer NOT NULL,
                    idmet integer NOT NULL,
                    arquivo_fonte_dados character varying(250) NOT NULL,
                    ignorar_reg_fonte date,
                    desconsiderar_reg_cred_maxima date,
                    data_hora timestamp without time zone NOT NULL
                );
                
                SELECT pg_catalog.setval(\'consistenciacao.confrontacao_idcon_seq\', 1, false);
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
        Schema::dropIfExists('consistenciacao.confrontacao');
    }
}
