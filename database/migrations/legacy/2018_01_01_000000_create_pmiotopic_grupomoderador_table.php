<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmiotopicGrupomoderadorTable extends Migration
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
                
                CREATE TABLE pmiotopic.grupomoderador (
                    ref_ref_cod_pessoa_fj integer NOT NULL,
                    ref_cod_grupos integer NOT NULL,
                    ref_pessoa_exc integer,
                    ref_pessoa_cad integer NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmiotopic.grupomoderador
                    ADD CONSTRAINT grupomoderador_pkey PRIMARY KEY (ref_ref_cod_pessoa_fj, ref_cod_grupos);
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
        Schema::dropIfExists('pmiotopic.grupomoderador');
    }
}
