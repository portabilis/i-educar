<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoGrupoFuncaoTable extends Migration
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
                
                CREATE TABLE acesso.grupo_funcao (
                    idmen integer NOT NULL,
                    idsis integer NOT NULL,
                    idgrp integer NOT NULL,
                    idfunc integer NOT NULL
                );
                
                ALTER TABLE ONLY acesso.grupo_funcao
                    ADD CONSTRAINT pk_grupo_funcao PRIMARY KEY (idmen, idsis, idgrp, idfunc);
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
        Schema::dropIfExists('acesso.grupo_funcao');
    }
}
