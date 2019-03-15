<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroPessoaFoneticoTable extends Migration
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
                
                CREATE TABLE cadastro.pessoa_fonetico (
                    idpes numeric(8,0) NOT NULL,
                    fonema character varying(30) NOT NULL
                );
                
                ALTER TABLE ONLY cadastro.pessoa_fonetico
                    ADD CONSTRAINT pk_pessoa_fonetico PRIMARY KEY (fonema, idpes);
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
        Schema::dropIfExists('cadastro.pessoa_fonetico');
    }
}
