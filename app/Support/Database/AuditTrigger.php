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
            'audit',
            'public.audit',
        ]);
    }

    /**
     * Return audited tables.
     *
     * @return array
     */
    public function getAuditedTables()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        return collect($tables)->sort()->reject(function ($table) {
            return in_array($table, $this->getSkippedTables());
        })->values()->toArray();
    }

    /**
     * Return create audit trigger SQL to the table.
     *
     * @param string $table
     *
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
     *
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
     *
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
     *
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
}
