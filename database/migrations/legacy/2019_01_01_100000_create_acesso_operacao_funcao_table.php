<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoOperacaoFuncaoTable extends Migration
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
                
                CREATE TABLE acesso.operacao_funcao (
                    idmen integer NOT NULL,
                    idsis integer NOT NULL,
                    idfunc integer NOT NULL,
                    idope integer NOT NULL
                );
                
                ALTER TABLE ONLY acesso.operacao_funcao
                    ADD CONSTRAINT pk_operacao_funcao PRIMARY KEY (idmen, idsis, idfunc, idope);
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
        Schema::dropIfExists('acesso.operacao_funcao');
    }
}
