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
            'INSERT INTO employee_posgraduate (
                employee_id,
                entity_id,
                type_id
            )
            SELECT
                cod_servidor,
                ref_cod_instituicao,
                unnest(pos_graduacao)
            FROM pmieducar.servidor
            WHERE true
            AND pos_graduacao is not null
            AND pos_graduacao != \'{}\';'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('truncate table employee_posgraduate;');
    }
};
