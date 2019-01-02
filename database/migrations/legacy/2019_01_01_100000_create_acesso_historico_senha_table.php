<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoHistoricoSenhaTable extends Migration
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
                
                CREATE TABLE acesso.historico_senha (
                    login character varying(16) NOT NULL,
                    senha character varying(60) NOT NULL,
                    data_cad timestamp without time zone NOT NULL
                );
                
                ALTER TABLE ONLY acesso.historico_senha
                    ADD CONSTRAINT pk_historico_senha PRIMARY KEY (login, senha);
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
        Schema::dropIfExists('acesso.historico_senha');
    }
}
