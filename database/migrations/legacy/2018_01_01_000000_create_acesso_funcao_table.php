<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoFuncaoTable extends Migration
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
                
                CREATE SEQUENCE acesso.funcao_idfunc_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE acesso.funcao (
                    idfunc integer DEFAULT nextval(\'acesso.funcao_idfunc_seq\'::regclass) NOT NULL,
                    idsis integer NOT NULL,
                    idmen integer NOT NULL,
                    nome character varying(100) NOT NULL,
                    situacao character(1) NOT NULL,
                    url character varying(250) NOT NULL,
                    ordem numeric(2,0) NOT NULL,
                    descricao character varying(250) NOT NULL,
                    CONSTRAINT ck_funcao_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                SELECT pg_catalog.setval(\'acesso.funcao_idfunc_seq\', 1, false);
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
        Schema::dropIfExists('acesso.funcao');
    }
}
