<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public $withinTransaction = false;

    public function up(): void
    {
        DB::statement(
            'UPDATE pmieducar.aluno
            SET tipo_transporte  = transporte_aluno.responsavel
            FROM modules.transporte_aluno
            WHERE transporte_aluno.aluno_id = cod_aluno
            AND transporte_aluno.responsavel != 0'
        );
    }

    public function down(): void
    {
    }
};
