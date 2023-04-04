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
        DB::unprepared(
            '
                ALTER TABLE pmieducar.turma
                ADD COLUMN estrutura_curricular integer[];
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
        DB::unprepared(
            '
                ALTER TABLE pmieducar.turma
                DROP COLUMN estrutura_curricular;
            '
        );
    }
};
