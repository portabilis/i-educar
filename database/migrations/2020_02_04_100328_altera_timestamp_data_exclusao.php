<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraTimestampDataExclusao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("ALTER TABLE pmieducar.matricula_turma ALTER COLUMN data_exclusao TYPE timestamp(0)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("ALTER TABLE pmieducar.matricula_turma ALTER COLUMN data_exclusao TYPE timestamp(4)");
    }
}
