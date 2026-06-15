<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.idx_historico_disciplinas_id;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.idx_historico_disciplinas_id1;');
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_historico_disciplinas_sequencial_ref_ref_cod_aluno_re;');

        Schema::table('pmieducar.historico_disciplinas', function (Blueprint $table) {
            $table->index(['ref_ref_cod_aluno', 'sequencial']);
        });
    }
};
