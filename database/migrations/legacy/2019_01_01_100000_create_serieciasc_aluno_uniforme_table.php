<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateSerieciascAlunoUniformeTable extends Migration
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
                
                CREATE TABLE serieciasc.aluno_uniforme (
                    ref_cod_aluno integer NOT NULL,
                    data_recebimento timestamp without time zone NOT NULL,
                    camiseta character(2),
                    quantidade_camiseta integer,
                    bermuda character(2),
                    quantidade_bermuda integer,
                    jaqueta character(2),
                    quantidade_jaqueta integer,
                    calca character(2),
                    quantidade_calca integer,
                    meia character(2),
                    quantidade_meia integer,
                    tenis character(2),
                    quantidade_tenis integer,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );
                
                ALTER TABLE ONLY serieciasc.aluno_uniforme
                    ADD CONSTRAINT aluno_uniforme_ref_cod_aluno_pk PRIMARY KEY (ref_cod_aluno, data_recebimento);
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
        Schema::dropIfExists('serieciasc.aluno_uniforme');
    }
}
