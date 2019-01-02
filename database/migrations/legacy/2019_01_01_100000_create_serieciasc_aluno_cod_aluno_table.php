<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascAlunoCodAlunoTable extends Migration
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
                SET default_with_oids = false;
                
                CREATE TABLE serieciasc.aluno_cod_aluno (
                    cod_aluno integer NOT NULL,
                    cod_ciasc bigint NOT NULL,
                    user_id integer,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY serieciasc.aluno_cod_aluno
                    ADD CONSTRAINT cod_aluno_serie_ref_cod_aluno_pk PRIMARY KEY (cod_aluno, cod_ciasc);
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
        Schema::dropIfExists('serieciasc.aluno_cod_aluno');
    }
}
