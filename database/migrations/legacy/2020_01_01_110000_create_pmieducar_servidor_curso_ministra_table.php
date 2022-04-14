<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePmieducarServidorCursoMinistraTable extends Migration
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
                CREATE TABLE pmieducar.servidor_curso_ministra (
                    ref_cod_curso integer NOT NULL,
                    ref_ref_cod_instituicao integer NOT NULL,
                    ref_cod_servidor integer NOT NULL
                );

                ALTER TABLE ONLY pmieducar.servidor_curso_ministra
                    ADD CONSTRAINT servidor_cuso_ministra_pkey PRIMARY KEY (ref_cod_curso, ref_ref_cod_instituicao, ref_cod_servidor);
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
        Schema::dropIfExists('pmieducar.servidor_curso_ministra');
    }
}
