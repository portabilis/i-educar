<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroAvisoNomeTable extends Migration
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
                
                CREATE TABLE cadastro.aviso_nome (
                    idpes numeric(8,0) NOT NULL,
                    aviso numeric(1,0) NOT NULL,
                    CONSTRAINT ck_aviso_nome_aviso CHECK (((aviso >= (1)::numeric) AND (aviso <= (4)::numeric)))
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
        Schema::dropIfExists('cadastro.aviso_nome');
    }
}
