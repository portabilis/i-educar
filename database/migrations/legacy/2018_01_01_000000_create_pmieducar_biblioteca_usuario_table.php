<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarBibliotecaUsuarioTable extends Migration
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
                
                CREATE TABLE pmieducar.biblioteca_usuario (
                    ref_cod_biblioteca integer NOT NULL,
                    ref_cod_usuario integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.biblioteca_usuario
                    ADD CONSTRAINT biblioteca_usuario_pkey PRIMARY KEY (ref_cod_biblioteca, ref_cod_usuario);
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
        Schema::dropIfExists('pmieducar.biblioteca_usuario');
    }
}
