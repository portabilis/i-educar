<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoOperacaoTable extends Migration
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
                
                CREATE TABLE acesso.operacao (
                    idope integer DEFAULT nextval(\'acesso.operacao_idope_seq\'::regclass) NOT NULL,
                    idsis integer,
                    nome character varying(40) NOT NULL,
                    situacao character(1) NOT NULL,
                    descricao character varying(250) NOT NULL,
                    CONSTRAINT ck_operacao_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
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
        Schema::dropIfExists('acesso.operacao');
    }
}
