<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCadastroEstadoCivilTable extends Migration
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
                CREATE TABLE cadastro.estado_civil (
                    ideciv numeric(1,0) NOT NULL,
                    descricao character varying(15) NOT NULL
                );

                ALTER TABLE ONLY cadastro.estado_civil
                    ADD CONSTRAINT pk_estado_civil PRIMARY KEY (ideciv);
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
        Schema::dropIfExists('cadastro.estado_civil');
    }
}
