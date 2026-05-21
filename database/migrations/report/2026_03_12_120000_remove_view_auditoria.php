<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS relatorio.view_auditoria;');
        DB::unprepared('DROP FUNCTION IF EXISTS relatorio.get_valor_campo_auditoria(character varying, character varying, character varying);');
    }
};
