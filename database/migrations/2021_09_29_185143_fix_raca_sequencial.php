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
        DB::unprepared('SELECT pg_catalog.setval(\'cadastro.raca_cod_raca_seq\', (select max(cod_raca) + 1 from cadastro.raca), true);');
    }
};
