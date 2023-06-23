<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS pmieducarmaterial_tipo_audit ON pmieducar.material_tipo;');
        DB::unprepared('DROP TABLE IF EXISTS pmieducar.material_tipo;');
        DB::unprepared('DROP SEQUENCE IF EXISTS pmieducar.material_tipo_cod_material_tipo_seq');
    }
};
