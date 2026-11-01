<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait AuditTrigger
{
    /**
     * Return not audited tables.
     *
     * @return array
     */
    public function getSkippedTables()
    {
        return config('audit.skip', [
            'public.ieducar_audit',
            'modules.auditoria',
            'modules.auditoria_geral',
            'portal.acesso',
            'cadastro.deficiencia_excluidos',
            'modules.area_conhecimento_excluidos',
            'modules.componente_curricular_ano_escolar_excluidos',
            'modules.componente_curricular_turma_excluidos',
            'modules.professor_turma_excluidos',
            'modules.regra_avaliacao_recuperacao_excluidos',
            'modules.regra_avaliacao_serie_ano_excluidos',
            'pmieducar.aluno_excluidos',
            'pmieducar.disciplina_dependencia_excluidos',
            'pmieducar.dispensa_disciplina_excluidos',
            'pmieducar.escola_serie_disciplina_excluidos',
            'pmieducar.matricula_turma_excluidos',
            'public.migrations',
            'public.reports_counts',
            'public.notifications',
            'public.job_batches',
            'public.failed_jobs',
            'public.password_resets',
            'public.log_unification_old_data',
            'public.educacenso_imports',
            'public.educacenso_inep_imports',
            'public.educacenso_situation_imports',
            'public.timelines',
        ]);
    }

    /**
     * Return audited tables.
     *
     * @return array
     */
    public function getAuditedTables()
    {
        $tables = DB::select('SELECT table_schema, table_name FROM information_schema.tables WHERE table_type = \'BASE TABLE\' AND table_schema IN (\'cadastro\', \'modules\', \'pmieducar\', \'portal\', \'public\', \'relatorio\');');

        $skipped = $this->getSkippedTables();

        $return = [];
        foreach ($tables as $table) {
            $qualified = $table->table_schema . '.' . $table->table_name;

            if (in_array($qualified, $skipped) || in_array($table->table_name, $skipped)) {
                continue;
            }

            $return[] = $qualified;
        }

        return $return;
    }

    /**
     * Return create audit trigger SQL to the table.
     *
     * @param string $table
     * @return string
     */
    public function getSqlForCreateAuditTrigger($table)
    {
        $trigger = Str::slug($table, '_') . '_audit';

        return <<<SQL
create trigger {$trigger}
after insert or update or delete on {$table}
for each row execute procedure public.audit();
SQL;
    }

    /**
     * Return drop audit trigger SQL to the table.
     *
     * @param string $table
     * @return string
     */
    public function getSqlForDropAuditTrigger($table)
    {
        $trigger = Str::slug($table, '_') . '_audit';

        return <<<SQL
drop trigger if exists {$trigger} on {$table};
SQL;
    }

    /**
     * Create audit trigger for table.
     *
     * @param string $table
     * @return void
     */
    public function createAuditTrigger($table)
    {
        DB::unprepared(
            $this->getSqlForCreateAuditTrigger($table)
        );
    }

    /**
     * Drop audit trigger from table.
     *
     * @param string $table
     * @return void
     */
    public function dropAuditTrigger($table)
    {
        DB::unprepared(
            $this->getSqlForDropAuditTrigger($table)
        );
    }

    /**
     * Create all audit triggers.
     *
     * @return void
     */
    public function createAuditTriggers()
    {
        foreach ($this->getAuditedTables() as $table) {
            $this->dropAuditTrigger($table);
            $this->createAuditTrigger($table);
        }
    }

    /**
     * Drop all audit triggers.
     *
     * @return void
     */
    public function dropAuditTriggers()
    {
        foreach ($this->getAuditedTables() as $table) {
            $this->dropAuditTrigger($table);
        }
    }

    /**
     * Return whether the audit function is installed.
     *
     * @return bool
     */
    public function auditFunctionExists()
    {
        return DB::selectOne(<<<'SQL'
            SELECT EXISTS (
                SELECT 1 FROM pg_proc p
                JOIN pg_namespace n ON n.oid = p.pronamespace
                WHERE n.nspname = 'public' AND p.proname = 'audit' AND p.pronargs = 0
            ) AS instalada
        SQL)->instalada;
    }

    /**
     * Return the audit triggers to drop and to create, by table.
     *
     * @return array<string, array{drop: string[], create: bool}>
     */
    public function getAuditTriggersDelta()
    {
        if (!$this->auditFunctionExists()) {
            return [];
        }

        $desired = $this->getAuditedTables();

        $existing = DB::select(<<<'SQL'
            SELECT n.nspname AS table_schema, c.relname AS table_name, t.tgname AS trigger_name
            FROM pg_trigger t
            JOIN pg_class c ON c.oid = t.tgrelid
            JOIN pg_namespace n ON n.oid = c.relnamespace
            WHERE t.tgfoid = 'public.audit()'::regprocedure
              AND NOT t.tgisinternal
              AND n.nspname IN ('cadastro', 'modules', 'pmieducar', 'portal', 'public', 'relatorio')
        SQL);

        $byTable = [];
        foreach ($existing as $trigger) {
            $byTable[$trigger->table_schema . '.' . $trigger->table_name][] = $trigger->trigger_name;
        }

        $delta = [];

        foreach (array_unique(array_merge(array_keys($byTable), $desired)) as $table) {
            $keep = in_array($table, $desired) ? Str::slug($table, '_') . '_audit' : null;
            $current = $byTable[$table] ?? [];

            $drop = array_values(array_filter($current, fn ($trigger) => $trigger !== $keep));
            $create = $keep !== null && !in_array($keep, $current);

            if ($drop === [] && !$create) {
                continue;
            }

            $delta[$table] = ['drop' => $drop, 'create' => $create];
        }

        return $delta;
    }

    /**
     * Apply the audit triggers delta.
     *
     * @return array{dropped: int, created: int}
     */
    public function reconcileAuditTriggers(bool $create = true)
    {
        $dropped = 0;
        $created = 0;

        foreach ($this->getAuditTriggersDelta() as $table => $change) {
            $willCreate = $create && $change['create'];

            if ($change['drop'] === [] && !$willCreate) {
                continue;
            }

            DB::transaction(function () use ($table, $change, $willCreate) {
                DB::unprepared("set local lock_timeout = '5s';");

                foreach ($change['drop'] as $trigger) {
                    $trigger = str_replace('"', '""', $trigger);

                    DB::unprepared("drop trigger if exists \"{$trigger}\" on {$table};");
                }

                if ($willCreate) {
                    $this->createAuditTrigger($table);
                }
            });

            $dropped += count($change['drop']);
            $created += (int) $willCreate;
        }

        return ['dropped' => $dropped, 'created' => $created];
    }
}
