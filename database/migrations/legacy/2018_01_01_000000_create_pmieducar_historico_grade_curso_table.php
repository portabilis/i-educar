<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreatePmieducarHistoricoGradeCursoTable extends Migration
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
                
                CREATE TABLE pmieducar.historico_grade_curso (
                    id integer DEFAULT nextval(\'pmieducar.historico_grade_curso_seq\'::regclass) NOT NULL,
                    descricao_etapa character varying(20) NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone,
                    quantidade_etapas integer,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
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
        Schema::dropIfExists('pmieducar.historico_grade_curso');
    }
}
