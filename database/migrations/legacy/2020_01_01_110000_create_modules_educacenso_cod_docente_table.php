<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesEducacensoCodDocenteTable extends Migration
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
                CREATE TABLE modules.educacenso_cod_docente (
                    cod_servidor integer NOT NULL,
                    cod_docente_inep bigint NOT NULL,
                    nome_inep character varying(255),
                    fonte character varying(255),
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                ALTER TABLE ONLY modules.educacenso_cod_docente
                    ADD CONSTRAINT educacenso_cod_docente_pk PRIMARY KEY (cod_servidor, cod_docente_inep);
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
        Schema::dropIfExists('modules.educacenso_cod_docente');
    }
}
