<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoGrupoMenuTable extends Migration
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
                
                CREATE TABLE acesso.grupo_menu (
                    idgrp integer NOT NULL,
                    idsis integer NOT NULL,
                    idmen integer NOT NULL
                );
                
                ALTER TABLE ONLY acesso.grupo_menu
                    ADD CONSTRAINT pk_grupo_menu PRIMARY KEY (idgrp, idsis, idmen);
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
        Schema::dropIfExists('acesso.grupo_menu');
    }
}
