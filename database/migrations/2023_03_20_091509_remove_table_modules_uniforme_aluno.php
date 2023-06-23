<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS modulesuniforme_aluno_audit ON modules.uniforme_aluno;');
        DB::unprepared('DROP TABLE IF EXISTS modules.uniforme_aluno;');
    }
};
