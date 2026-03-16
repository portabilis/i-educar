<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_dados_historico_posicionamento;'
        );
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_series_anos_extra_curricular;'
        );
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_series_anos;'
        );
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_eja_extra_curricular'
        );
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_eja'
        );
        DB::unprepared(
            'DROP VIEW IF EXISTS relatorio.view_historico_9anos'
        );
    }
};
