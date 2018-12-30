<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalPessoaAtividadeTable extends Migration
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

                CREATE TABLE portal.pessoa_atividade (
                    cod_pessoa_atividade integer DEFAULT nextval(\'portal.pessoa_atividade_cod_pessoa_atividade_seq\'::regclass) NOT NULL,
                    ref_cod_ramo_atividade integer DEFAULT 0 NOT NULL,
                    nm_atividade character varying(255)
                );
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
        Schema::dropIfExists('portal.pessoa_atividade');
    }
}
