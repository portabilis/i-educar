<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS relatorio.view_modulo;');
        DB::unprepared('DROP VIEW IF EXISTS relatorio.view_dados_modulo;');
    }
};
