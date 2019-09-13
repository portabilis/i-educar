<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroEscolaridadeTable extends Migration
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
                
                CREATE TABLE cadastro.escolaridade (
                    idesco numeric(2,0) NOT NULL,
                    descricao character varying(60) NOT NULL,
                    escolaridade smallint
                );
                
                ALTER TABLE ONLY cadastro.escolaridade
                    ADD CONSTRAINT pk_escolaridade PRIMARY KEY (idesco);
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
        Schema::dropIfExists('cadastro.escolaridade');
    }
}
