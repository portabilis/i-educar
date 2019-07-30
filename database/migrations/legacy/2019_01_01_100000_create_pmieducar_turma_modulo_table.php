<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarTurmaModuloTable extends Migration
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
                
                CREATE TABLE pmieducar.turma_modulo (
                    ref_cod_turma integer NOT NULL,
                    ref_cod_modulo integer NOT NULL,
                    sequencial integer NOT NULL,
                    data_inicio date NOT NULL,
                    data_fim date NOT NULL,
                    dias_letivos integer
                );
                
                ALTER TABLE ONLY pmieducar.turma_modulo
                    ADD CONSTRAINT turma_modulo_pkey PRIMARY KEY (ref_cod_turma, ref_cod_modulo, sequencial);
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
        Schema::dropIfExists('pmieducar.turma_modulo');
    }
}
