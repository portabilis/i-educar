<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateConsistenciacaoTipoIncoerenciaTable extends Migration
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
                
                CREATE TABLE consistenciacao.tipo_incoerencia (
                    id_tipo_inc numeric(3,0) NOT NULL,
                    idcam numeric(3,0) NOT NULL,
                    descricao character varying(250) NOT NULL
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
        Schema::dropIfExists('consistenciacao.tipo_incoerencia');
    }
}
