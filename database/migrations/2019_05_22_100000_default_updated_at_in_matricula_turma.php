<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DefaultUpdatedAtInMatriculaTurma extends Migration
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
                ALTER TABLE pmieducar.matricula_turma ALTER COLUMN updated_at SET DEFAULT now();
            '
        );
    }
}
