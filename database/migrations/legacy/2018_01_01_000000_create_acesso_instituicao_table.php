<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoInstituicaoTable extends Migration
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
                
                CREATE SEQUENCE acesso.instituicao_idins_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE acesso.instituicao (
                    idins integer DEFAULT nextval(\'acesso.instituicao_idins_seq\'::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    situacao character(1) NOT NULL,
                    CONSTRAINT ck_instituicao_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                SELECT pg_catalog.setval(\'acesso.instituicao_idins_seq\', 1, false);
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
        Schema::dropIfExists('acesso.instituicao');
    }
}
