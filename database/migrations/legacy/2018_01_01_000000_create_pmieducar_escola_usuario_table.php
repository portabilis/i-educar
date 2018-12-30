<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarEscolaUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # FIXME

        DB::unprepared(
            '
                SET default_with_oids = false;

                CREATE TABLE pmieducar.escola_usuario (
                    id integer NOT NULL,
                    ref_cod_usuario integer NOT NULL,
                    ref_cod_escola integer NOT NULL,
                    escola_atual integer
                );

                -- ALTER SEQUENCE pmieducar.escola_usuario_id_seq OWNED BY pmieducar.escola_usuario.id;
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
        Schema::dropIfExists('pmieducar.escola_usuario');
    }
}
