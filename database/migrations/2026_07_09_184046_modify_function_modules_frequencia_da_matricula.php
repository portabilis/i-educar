<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            file_get_contents(database_path('sqls/functions/modules.frequencia_da_matricula.sql'))
        );
    }
};
