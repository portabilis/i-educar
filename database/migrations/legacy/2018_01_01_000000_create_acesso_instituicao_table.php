<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoInstituicaoTable extends Migration
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
                
                CREATE TABLE acesso.instituicao (
                    idins integer DEFAULT nextval(\'acesso.instituicao_idins_seq\'::regclass) NOT NULL,
                    nome character varying(60) NOT NULL,
                    situacao character(1) NOT NULL,
                    CONSTRAINT ck_instituicao_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
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
        Schema::dropIfExists('acesso.instituicao');
    }
}
