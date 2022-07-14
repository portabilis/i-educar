<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE pmieducar.turma ADD COLUMN formas_organizacao_turma int2;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE pmieducar.turma DROP COLUMN formas_organizacao_turma;');
    }
};
