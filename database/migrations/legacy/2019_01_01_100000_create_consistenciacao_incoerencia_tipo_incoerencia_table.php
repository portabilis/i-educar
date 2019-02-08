<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoIncoerenciaTipoIncoerenciaTable extends Migration
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
                
                CREATE TABLE consistenciacao.incoerencia_tipo_incoerencia (
                    id_tipo_inc numeric(3,0) NOT NULL,
                    idinc integer NOT NULL
                );
                
                ALTER TABLE ONLY consistenciacao.incoerencia_tipo_incoerencia
                    ADD CONSTRAINT pk_incoerencia_tipo_incoerencia PRIMARY KEY (id_tipo_inc, idinc);
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
        Schema::dropIfExists('consistenciacao.incoerencia_tipo_incoerencia');
    }
}
