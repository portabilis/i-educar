<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesTransporteAlunoTable extends Migration
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
                CREATE TABLE modules.transporte_aluno (
                    aluno_id integer NOT NULL,
                    responsavel integer NOT NULL,
                    user_id integer NOT NULL,
                    created_at timestamp without time zone NOT NULL,
                    updated_at timestamp without time zone
                );

                ALTER TABLE ONLY modules.transporte_aluno
                    ADD CONSTRAINT transporte_aluno_pk PRIMARY KEY (aluno_id);
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
        Schema::dropIfExists('modules.transporte_aluno');
    }
}
