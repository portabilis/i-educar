<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesNotaComponenteCurricularMediaTable extends Migration
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
                CREATE TABLE modules.nota_componente_curricular_media (
                    nota_aluno_id integer NOT NULL,
                    componente_curricular_id integer NOT NULL,
                    media numeric(8,4) DEFAULT 0,
                    media_arredondada character varying(10) DEFAULT 0,
                    etapa character varying(2) NOT NULL,
                    situacao integer,
	                bloqueada bool NOT NULL DEFAULT false
                );

                ALTER TABLE ONLY modules.nota_componente_curricular_media
                    ADD CONSTRAINT nota_componente_curricular_media_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id);
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
        Schema::dropIfExists('modules.nota_componente_curricular_media');
    }
}
