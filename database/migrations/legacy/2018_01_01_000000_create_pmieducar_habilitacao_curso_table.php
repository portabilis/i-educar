<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarHabilitacaoCursoTable extends Migration
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
                
                CREATE TABLE pmieducar.habilitacao_curso (
                    ref_cod_habilitacao integer NOT NULL,
                    ref_cod_curso integer NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.habilitacao_curso
                    ADD CONSTRAINT habilitacao_curso_pkey PRIMARY KEY (ref_cod_habilitacao, ref_cod_curso);
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
        Schema::dropIfExists('pmieducar.habilitacao_curso');
    }
}
