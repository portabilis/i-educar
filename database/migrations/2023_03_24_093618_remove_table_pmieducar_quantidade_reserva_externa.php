<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarquantidade_reserva_externa_audit ON pmieducar.quantidade_reserva_externa;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.quantidade_reserva_externa;');
    }
};
