<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveStudentBenefitPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE pmieducar.aluno_aluno_beneficio DROP CONSTRAINT IF EXISTS aluno_aluno_beneficio_pk;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE pmieducar.aluno_aluno_beneficio ADD CONSTRAINT aluno_aluno_beneficio_pk PRIMARY KEY(aluno_id, aluno_beneficio_id);');
    }
}
