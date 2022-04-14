<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroFisicaRacaTable extends Migration
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
                CREATE TABLE cadastro.fisica_raca (
                    ref_idpes integer NOT NULL,
                    ref_cod_raca integer NOT NULL
                );

                ALTER TABLE ONLY cadastro.fisica_raca
                    ADD CONSTRAINT pk_fisica_raca PRIMARY KEY (ref_idpes);
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
        Schema::dropIfExists('cadastro.fisica_raca');
    }
}
