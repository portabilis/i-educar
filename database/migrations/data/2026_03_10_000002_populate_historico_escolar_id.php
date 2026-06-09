<?php

use App\Support\Database\EnableDisableForeignKeys;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    use EnableDisableForeignKeys;

    public $withinTransaction = false;

    public function up(): void
    {
        $this->disableForeignKeys('pmieducar.historico_disciplinas');

        DB::statement('
            UPDATE pmieducar.historico_disciplinas hd
            SET historico_escolar_id = he.id
            FROM pmieducar.historico_escolar he
            WHERE hd.ref_ref_cod_aluno = he.ref_cod_aluno
              AND hd.ref_sequencial = he.sequencial
        ');

        $this->enableForeignKeys('pmieducar.historico_disciplinas');
    }

    public function down(): void
    {
        DB::statement('
            UPDATE pmieducar.historico_disciplinas
            SET historico_escolar_id = NULL
        ');
    }
};
