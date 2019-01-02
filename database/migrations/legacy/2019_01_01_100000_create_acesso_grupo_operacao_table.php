<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoGrupoOperacaoTable extends Migration
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
                
                CREATE TABLE acesso.grupo_operacao (
                    idfunc integer NOT NULL,
                    idgrp integer NOT NULL,
                    idsis integer NOT NULL,
                    idmen integer NOT NULL,
                    idope integer NOT NULL
                );
                
                ALTER TABLE ONLY acesso.grupo_operacao
                    ADD CONSTRAINT pk_grupo_operacao PRIMARY KEY (idfunc, idgrp, idsis, idmen, idope);
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
        Schema::dropIfExists('acesso.grupo_operacao');
    }
}
