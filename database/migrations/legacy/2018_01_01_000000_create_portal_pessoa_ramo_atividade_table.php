<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePortalPessoaRamoAtividadeTable extends Migration
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

                CREATE TABLE portal.pessoa_ramo_atividade (
                    cod_ramo_atividade integer DEFAULT nextval(\'portal.pessoa_ramo_atividade_cod_ramo_atividade_seq\'::regclass) NOT NULL,
                    nm_ramo_atividade character varying(255)
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
        Schema::dropIfExists('portal.pessoa_ramo_atividade');
    }
}
