<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'ALTER TABLE modules.componente_curricular_ano_escolar ALTER COLUMN hora_falta TYPE numeric(7, 4) USING hora_falta::numeric;'
        );

        DB::unprepared(
            'ALTER TABLE pmieducar.escola_serie_disciplina ALTER COLUMN hora_falta TYPE numeric(7, 4) USING hora_falta::numeric;'
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
            'ALTER TABLE modules.componente_curricular_ano_escolar ALTER COLUMN hora_falta TYPE numeric(7, 3) USING hora_falta::numeric;'
        );

        DB::unprepared(
            'ALTER TABLE pmieducar.escola_serie_disciplina ALTER COLUMN hora_falta TYPE numeric(7, 3) USING hora_falta::numeric;'
        );
    }
};
