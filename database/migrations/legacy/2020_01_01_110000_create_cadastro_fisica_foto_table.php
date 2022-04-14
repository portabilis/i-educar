<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroFisicaFotoTable extends Migration
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
                CREATE TABLE cadastro.fisica_foto (
                    idpes integer NOT NULL,
                    caminho character varying(255),
	                updated_at timestamp NULL DEFAULT now()
                );

                ALTER TABLE ONLY cadastro.fisica_foto
                    ADD CONSTRAINT fisica_foto_pkey PRIMARY KEY (idpes);
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
        Schema::dropIfExists('cadastro.fisica_foto');
    }
}
