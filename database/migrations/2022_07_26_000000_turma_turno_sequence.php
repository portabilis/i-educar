<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            '
                SELECT pg_catalog.setval(\'pmieducar.turma_turno_id_seq\', (select max(id) from pmieducar.turma_turno), true);
            '
        );
    }
};
