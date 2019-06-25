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
	                menu_id int4 NOT NULL,
                    cadastra smallint DEFAULT 0 NOT NULL,
                    visualiza smallint DEFAULT 0 NOT NULL,
                    exclui smallint DEFAULT 0 NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.menu_tipo_usuario
                    ADD CONSTRAINT menu_tipo_usuario_pkey PRIMARY KEY (ref_cod_tipo_usuario, menu_id);
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
