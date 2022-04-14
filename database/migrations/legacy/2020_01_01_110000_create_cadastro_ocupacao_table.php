<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroOcupacaoTable extends Migration
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
                CREATE TABLE cadastro.ocupacao (
                    idocup numeric(6,0) NOT NULL,
                    descricao character varying(250) NOT NULL
                );

                ALTER TABLE ONLY cadastro.ocupacao
                    ADD CONSTRAINT pk_ocupacao PRIMARY KEY (idocup);
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
        Schema::dropIfExists('cadastro.ocupacao');
    }
}
