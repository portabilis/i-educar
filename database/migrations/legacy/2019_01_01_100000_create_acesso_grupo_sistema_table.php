<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoGrupoSistemaTable extends Migration
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
                
                CREATE TABLE acesso.grupo_sistema (
                    idsis integer NOT NULL,
                    idgrp integer NOT NULL
                );
                
                ALTER TABLE ONLY acesso.grupo_sistema
                    ADD CONSTRAINT pk_grupo_sistema PRIMARY KEY (idsis, idgrp);
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
        Schema::dropIfExists('acesso.grupo_sistema');
    }
}
