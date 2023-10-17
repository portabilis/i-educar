<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (Schema::hasTable('modules.transporte_aluno') === false) {
            return;
        }

        DB::statement(
            'UPDATE pmieducar.aluno
            SET tipo_transporte  = transporte_aluno.responsavel
            FROM modules.transporte_aluno
            WHERE transporte_aluno.aluno_id = cod_aluno
            AND transporte_aluno.responsavel != 0'
        );
    }
};
