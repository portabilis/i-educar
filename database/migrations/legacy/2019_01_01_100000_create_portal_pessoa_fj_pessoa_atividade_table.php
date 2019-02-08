<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalPessoaFjPessoaAtividadeTable extends Migration
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

                CREATE TABLE portal.pessoa_fj_pessoa_atividade (
                    ref_cod_pessoa_atividade integer DEFAULT 0 NOT NULL,
                    ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY portal.pessoa_fj_pessoa_atividade
                    ADD CONSTRAINT pessoa_fj_pessoa_atividade_pk PRIMARY KEY (ref_cod_pessoa_atividade, ref_cod_pessoa_fj);
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
        Schema::dropIfExists('portal.pessoa_fj_pessoa_atividade');
    }
}
