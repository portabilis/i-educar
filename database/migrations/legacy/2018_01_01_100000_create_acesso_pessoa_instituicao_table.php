<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAcessoPessoaInstituicaoTable extends Migration
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
                
                CREATE TABLE acesso.pessoa_instituicao (
                    idins integer NOT NULL,
                    idpes numeric(8,0) NOT NULL
                );
                
                ALTER TABLE ONLY acesso.pessoa_instituicao
                    ADD CONSTRAINT pk_pessoa_instituicao PRIMARY KEY (idins, idpes);
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
        Schema::dropIfExists('acesso.pessoa_instituicao');
    }
}
