<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroFisicaDeficienciaTable extends Migration
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
                
                CREATE TABLE cadastro.fisica_deficiencia (
                    ref_idpes integer NOT NULL,
                    ref_cod_deficiencia integer NOT NULL
                );
                
                ALTER TABLE ONLY cadastro.fisica_deficiencia
                    ADD CONSTRAINT pk_fisica_deficiencia PRIMARY KEY (ref_idpes, ref_cod_deficiencia);
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
        Schema::dropIfExists('cadastro.fisica_deficiencia');
    }
}
