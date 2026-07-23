<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            file_get_contents(database_path('sqls/functions/relatorio.get_nome_escola.sql'))
        );
    }
};
