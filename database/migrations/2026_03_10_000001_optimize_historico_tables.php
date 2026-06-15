<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pmieducar.historico_disciplinas ADD COLUMN historico_escolar_id INTEGER');

        DB::statement('
            CREATE INDEX idx_historico_disciplinas_historico_escolar_id
            ON pmieducar.historico_disciplinas (historico_escolar_id)
        ');

        DB::statement('
            ALTER TABLE pmieducar.historico_disciplinas
            ADD CONSTRAINT fk_historico_disciplinas_historico_escolar
            FOREIGN KEY (historico_escolar_id) REFERENCES pmieducar.historico_escolar(id)
            ON UPDATE CASCADE ON DELETE CASCADE
        ');

        // Remover índices redundantes do historico_escolar
        DB::statement('DROP INDEX IF EXISTS pmieducar.idx_historico_escolar_id1');
        DB::statement('DROP INDEX IF EXISTS pmieducar.idx_historico_escolar_id2');
        DB::statement('DROP INDEX IF EXISTS pmieducar.idx_historico_escolar_id3');
        DB::statement('DROP INDEX IF EXISTS pmieducar.idx_historico_escolar_aluno_ativo');
        DB::statement('DROP INDEX IF EXISTS pmieducar.historico_escolar_nm_serie_idx');
        DB::statement('DROP INDEX IF EXISTS pmieducar.historico_escolar_ano_idx');
        DB::statement('DROP INDEX IF EXISTS pmieducar.historico_escolar_ativo_idx');

        // Remover FK antiga da composite key (existe em alguns bancos)
        DB::statement('
            ALTER TABLE pmieducar.historico_disciplinas
            DROP CONSTRAINT IF EXISTS historico_disciplinas_ref_ref_cod_aluno_fkey
        ');

        // Remover constraint UNIQUE invertida (duplicata da UNIQUE original com colunas invertidas)
        DB::statement('
            ALTER TABLE pmieducar.historico_escolar
            DROP CONSTRAINT IF EXISTS pmieducar_historico_escolar_sequencial_ref_cod_aluno_unique
        ');

        // Remover FK duplicada de ref_cod_escola (fkey e fkey1 são idênticas)
        DB::statement('
            ALTER TABLE pmieducar.historico_escolar
            DROP CONSTRAINT IF EXISTS historico_escolar_ref_cod_escola_fkey1
        ');
    }

    public function down(): void
    {
        // Recriar FK duplicada
        DB::statement('
            ALTER TABLE pmieducar.historico_escolar
            ADD CONSTRAINT historico_escolar_ref_cod_escola_fkey1
            FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola)
        ');

        // Recriar constraint UNIQUE invertida
        DB::statement('
            ALTER TABLE pmieducar.historico_escolar
            ADD CONSTRAINT pmieducar_historico_escolar_sequencial_ref_cod_aluno_unique
            UNIQUE (sequencial, ref_cod_aluno)
        ');

        // Recriar índices do historico_escolar
        DB::statement('CREATE INDEX historico_escolar_ativo_idx ON pmieducar.historico_escolar (ativo)');
        DB::statement('CREATE INDEX historico_escolar_ano_idx ON pmieducar.historico_escolar (ano)');
        DB::statement('CREATE INDEX historico_escolar_nm_serie_idx ON pmieducar.historico_escolar (nm_serie)');
        DB::statement('CREATE INDEX idx_historico_escolar_aluno_ativo ON pmieducar.historico_escolar (ref_cod_aluno, ativo)');
        DB::statement('CREATE INDEX idx_historico_escolar_id3 ON pmieducar.historico_escolar (ref_cod_aluno, ano)');
        DB::statement('CREATE INDEX idx_historico_escolar_id2 ON pmieducar.historico_escolar (ref_cod_aluno, sequencial, ano)');
        DB::statement('CREATE INDEX idx_historico_escolar_id1 ON pmieducar.historico_escolar (ref_cod_aluno, sequencial)');

        // Remover FK e índice da nova coluna
        DB::statement('ALTER TABLE pmieducar.historico_disciplinas DROP CONSTRAINT IF EXISTS fk_historico_disciplinas_historico_escolar');
        DB::statement('DROP INDEX IF EXISTS pmieducar.idx_historico_disciplinas_historico_escolar_id');

        DB::statement('ALTER TABLE pmieducar.historico_disciplinas DROP COLUMN IF EXISTS historico_escolar_id');
    }
};
