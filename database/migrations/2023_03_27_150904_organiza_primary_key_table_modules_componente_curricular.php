<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular ADD CONSTRAINT componente_curricular_pkey PRIMARY KEY (id);');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular ADD CONSTRAINT componente_curricular_pkey PRIMARY KEY (id, instituicao_id);');
    }
};
