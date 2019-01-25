<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoUsuarioGrupoTable extends Migration
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
                
                CREATE TABLE acesso.usuario_grupo (
                    idgrp integer NOT NULL,
                    login character varying(16) NOT NULL
                );
                
                ALTER TABLE ONLY acesso.usuario_grupo
                    ADD CONSTRAINT pk_usuario_grupo PRIMARY KEY (idgrp, login);
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
        Schema::dropIfExists('acesso.usuario_grupo');
    }
}
