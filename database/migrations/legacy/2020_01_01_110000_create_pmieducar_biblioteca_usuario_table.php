<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                CREATE TABLE pmieducar.biblioteca_usuario (
                    ref_cod_biblioteca integer NOT NULL,
                    ref_cod_usuario integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.biblioteca_usuario
                    ADD CONSTRAINT biblioteca_usuario_pkey PRIMARY KEY (ref_cod_biblioteca, ref_cod_usuario);

                CREATE INDEX fki_biblioteca_usuario_ref_cod_biblioteca_fk ON pmieducar.biblioteca_usuario USING btree (ref_cod_biblioteca);
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
