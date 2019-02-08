<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoGrupoTable extends Migration
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
                
                CREATE SEQUENCE acesso.grupo_idgrp_seq
                    START WITH 1
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE acesso.grupo (
                    idgrp integer DEFAULT nextval(\'acesso.grupo_idgrp_seq\'::regclass) NOT NULL,
                    nome character varying(40) NOT NULL,
                    situacao character(1) NOT NULL,
                    descricao character varying(250),
                    CONSTRAINT ck_grupo_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                ALTER TABLE ONLY acesso.grupo
                    ADD CONSTRAINT pk_grupo PRIMARY KEY (idgrp);
                    
                SELECT pg_catalog.setval(\'acesso.grupo_idgrp_seq\', 1, false);
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
        Schema::dropIfExists('acesso.grupo');

        DB::unprepared('DROP SEQUENCE acesso.grupo_idgrp_seq');
    }
}
