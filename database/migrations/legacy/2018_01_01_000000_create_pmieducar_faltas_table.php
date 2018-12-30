<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarFaltasTable extends Migration
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
                SET default_with_oids = false;

                CREATE TABLE pmieducar.faltas (
                    ref_cod_matricula integer NOT NULL,
                    sequencial integer DEFAULT nextval(\'pmieducar.faltas_sequencial_seq\'::regclass) NOT NULL,
                    ref_usuario_cad integer NOT NULL,
                    falta integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL
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
        Schema::dropIfExists('pmieducar.faltas');
    }
}
