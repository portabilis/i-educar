<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarmaterial_didatico_audit ON pmieducar.material_didatico;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.material_didatico;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.material_didatico_cod_material_didatico_seq');
    }
};
