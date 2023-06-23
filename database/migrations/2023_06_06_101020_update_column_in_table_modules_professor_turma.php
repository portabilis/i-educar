<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::unprepared('ALTER TABLE IF EXISTS modules.professor_turma ALTER COLUMN outras_unidades_curriculares_obrigatorias DROP NOT NULL;');
        DB::unprepared('UPDATE modules.professor_turma SET outras_unidades_curriculares_obrigatorias = null WHERE outras_unidades_curriculares_obrigatorias = 0;');
    }
};
