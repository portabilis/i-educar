<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesEducacensoCodTurmaTable extends Migration
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
                CREATE TABLE modules.educacenso_cod_turma (
                    cod_turma integer NOT NULL,
                    cod_turma_inep bigint NOT NULL,
                    nome_inep character varying(255),
                    fonte character varying(255),
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                ALTER TABLE ONLY modules.educacenso_cod_turma
                    ADD CONSTRAINT educacenso_cod_turma_pk PRIMARY KEY (cod_turma, cod_turma_inep);
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
        Schema::dropIfExists('modules.educacenso_cod_turma');
    }
}
