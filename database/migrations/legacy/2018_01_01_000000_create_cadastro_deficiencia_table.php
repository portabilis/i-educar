<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroDeficienciaTable extends Migration
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
                
                CREATE TABLE cadastro.deficiencia (
                    cod_deficiencia integer DEFAULT nextval(\'cadastro.deficiencia_cod_deficiencia_seq\'::regclass) NOT NULL,
                    nm_deficiencia character varying(70) NOT NULL,
                    deficiencia_educacenso smallint,
                    desconsidera_regra_diferenciada boolean DEFAULT false
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
        Schema::dropIfExists('cadastro.deficiencia');
    }
}
