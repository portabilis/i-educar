<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
