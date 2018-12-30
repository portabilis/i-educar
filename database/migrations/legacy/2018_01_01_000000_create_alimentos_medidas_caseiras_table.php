<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAlimentosMedidasCaseirasTable extends Migration
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
                
                CREATE TABLE alimentos.medidas_caseiras (
                    idmedcas character varying(20) NOT NULL,
                    idcli character varying(10) NOT NULL,
                    descricao character varying(50) NOT NULL
                );
                
                ALTER TABLE ONLY alimentos.medidas_caseiras
                    ADD CONSTRAINT pk_medidas_caseiras PRIMARY KEY (idmedcas, idcli);
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
        Schema::dropIfExists('alimentos.medidas_caseiras');
    }
}
