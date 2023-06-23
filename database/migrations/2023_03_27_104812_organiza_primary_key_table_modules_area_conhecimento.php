<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_area_conhecimento_fk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS modules_componente_curricular_area_conhecimento_id_instituicao_;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_area_conhecimento_id_instituicao_id_forei;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.area_conhecimento DROP CONSTRAINT IF EXISTS area_conhecimento_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.area_conhecimento ADD CONSTRAINT area_conhecimento_pkey PRIMARY KEY (id);');

        DB::unprepared('
            ALTER TABLE IF EXISTS modules.componente_curricular
            ADD CONSTRAINT componente_curricular_area_conhecimento_id_instituicao_id_forei FOREIGN KEY (area_conhecimento_id)
            REFERENCES modules.area_conhecimento (id) MATCH SIMPLE
            ON UPDATE RESTRICT
            ON DELETE RESTRICT;
        ');
    }

    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_area_conhecimento_id_instituicao_id_forei;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.componente_curricular DROP CONSTRAINT IF EXISTS componente_curricular_area_conhecimento_fk;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.area_conhecimento DROP CONSTRAINT IF EXISTS area_conhecimento_pkey;');
        DB::unprepared('ALTER TABLE IF EXISTS modules.area_conhecimento ADD CONSTRAINT area_conhecimento_pkey PRIMARY KEY (id, instituicao_id);');
        DB::unprepared('
            ALTER TABLE IF EXISTS modules.componente_curricular
            ADD CONSTRAINT componente_curricular_area_conhecimento_fk FOREIGN KEY (area_conhecimento_id, instituicao_id)
            REFERENCES modules.area_conhecimento (id, instituicao_id) MATCH SIMPLE
            ON UPDATE RESTRICT
            ON DELETE RESTRICT;
        ');
    }
};
