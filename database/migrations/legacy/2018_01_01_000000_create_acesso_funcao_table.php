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
