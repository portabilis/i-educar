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
                
                CREATE SEQUENCE pmieducar.historico_grade_curso_seq
                    START WITH 3
                    INCREMENT BY 1
                    MINVALUE 0
                    NO MAXVALUE
                    CACHE 1;

                CREATE TABLE pmieducar.historico_grade_curso (
                    id integer DEFAULT nextval(\'pmieducar.historico_grade_curso_seq\'::regclass) NOT NULL,
                    descricao_etapa character varying(20) NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone,
                    quantidade_etapas integer,
                    ativo smallint DEFAULT (1)::smallint NOT NULL
                );
                
                ALTER TABLE ONLY pmieducar.historico_grade_curso
                    ADD CONSTRAINT historico_grade_curso_pk PRIMARY KEY (id);

                SELECT pg_catalog.setval(\'pmieducar.historico_grade_curso_seq\', 3, false);
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

        DB::unprepared('DROP SEQUENCE pmieducar.historico_grade_curso_seq;');
    }
}
