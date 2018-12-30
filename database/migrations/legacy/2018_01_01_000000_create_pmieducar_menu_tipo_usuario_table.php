<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMenuTipoUsuarioTable extends Migration
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
                
                CREATE TABLE pmieducar.menu_tipo_usuario (
                    ref_cod_tipo_usuario integer NOT NULL,
                    ref_cod_menu_submenu integer NOT NULL,
                    cadastra smallint DEFAULT 0 NOT NULL,
                    visualiza smallint DEFAULT 0 NOT NULL,
                    exclui smallint DEFAULT 0 NOT NULL
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
        Schema::dropIfExists('pmieducar.menu_tipo_usuario');
    }
}
