<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateUrbanoTipoLogradouroTable extends Migration
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
                
                CREATE TABLE urbano.tipo_logradouro (
                    idtlog character varying(5) NOT NULL,
                    descricao character varying(40) NOT NULL
                );
                
                ALTER TABLE ONLY urbano.tipo_logradouro
                    ADD CONSTRAINT pk_tipo_logradouro PRIMARY KEY (idtlog);
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
        Schema::dropIfExists('urbano.tipo_logradouro');
    }
}
