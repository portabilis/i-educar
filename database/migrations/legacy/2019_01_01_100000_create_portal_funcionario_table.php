<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalFuncionarioTable extends Migration
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

                CREATE TABLE portal.funcionario (
                    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
                    matricula character varying(12),
                    senha varchar(191) NULL,
                    ativo smallint,
                    ref_sec integer,
                    ramal character varying(10),
                    sequencial character(3),
                    opcao_menu text,
                    ref_cod_setor integer,
                    ref_cod_funcionario_vinculo integer,
                    tempo_expira_senha integer,
                    tempo_expira_conta integer,
                    data_troca_senha date,
                    data_reativa_conta date,
                    ref_ref_cod_pessoa_fj integer,
                    proibido integer DEFAULT 0 NOT NULL,
                    ref_cod_setor_new integer,
                    matricula_new bigint,
                    matricula_permanente smallint DEFAULT 0,
                    tipo_menu smallint DEFAULT 0 NOT NULL,
                    ip_logado character varying(50),
                    data_login timestamp without time zone,
                    email character varying(50),
                    status_token character varying(191) NULL,
                    matricula_interna character varying(30),
                    receber_novidades smallint,
                    atualizou_cadastro smallint
                );
                
                ALTER TABLE ONLY portal.funcionario
                    ADD CONSTRAINT funcionario_pk PRIMARY KEY (ref_cod_pessoa_fj);
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
        Schema::dropIfExists('portal.funcionario');
    }
}
