<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoUsuarioTable extends Migration
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
                
                CREATE TABLE acesso.usuario (
                    login character varying(16) NOT NULL,
                    idpes numeric(8,0) NOT NULL,
                    idpes_sga numeric(8,0),
                    senha character varying(60) NOT NULL,
                    datacad date NOT NULL,
                    lastlogin timestamp without time zone NOT NULL,
                    dica character varying(60),
                    situacao character(1) NOT NULL,
                    data_alt_senha date NOT NULL,
                    exp_senha character(1),
                    mudar_senha character(1),
                    num_sessao_atual numeric(2,0) NOT NULL,
                    prazo_exp numeric(3,0),
                    estilo_menu character(1),
                    CONSTRAINT ck_usuario_estilo_menu CHECK (((estilo_menu = \'C\'::bpchar) OR (estilo_menu = \'D\'::bpchar))),
                    CONSTRAINT ck_usuario_exp_senha CHECK (((exp_senha = \'S\'::bpchar) OR (exp_senha = \'N\'::bpchar))),
                    CONSTRAINT ck_usuario_mudar_senha CHECK (((mudar_senha = \'S\'::bpchar) OR (mudar_senha = \'N\'::bpchar))),
                    CONSTRAINT ck_usuario_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
                
                ALTER TABLE ONLY acesso.usuario
                    ADD CONSTRAINT pk_usuario PRIMARY KEY (login);
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
        Schema::dropIfExists('acesso.usuario');
    }
}
