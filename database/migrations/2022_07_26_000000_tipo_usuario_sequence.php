<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            '
                SELECT pg_catalog.setval(\'pmieducar.tipo_usuario_cod_tipo_usuario_seq\', (select max(cod_tipo_usuario) from pmieducar.tipo_usuario), true);
            '
        );
    }
};
