<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarMatriculaOcorrenciaDisciplinarTable extends Migration
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
                
                CREATE TABLE pmieducar.matricula_ocorrencia_disciplinar (
                    ref_cod_matricula integer NOT NULL,
                    ref_cod_tipo_ocorrencia_disciplinar integer NOT NULL,
                    sequencial integer NOT NULL,
                    ref_usuario_exc integer,
                    ref_usuario_cad integer NOT NULL,
                    observacao text NOT NULL,
                    data_cadastro timestamp without time zone NOT NULL,
                    data_exclusao timestamp without time zone,
                    ativo smallint DEFAULT (1)::smallint NOT NULL,
                    visivel_pais integer,
                    cod_ocorrencia_disciplinar integer DEFAULT nextval(\'pmieducar.ocorrencia_disciplinar_seq\'::regclass) NOT NULL
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
        Schema::dropIfExists('pmieducar.matricula_ocorrencia_disciplinar');
    }
}
