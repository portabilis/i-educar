<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait DropPrimaryKey
{
    /**
     * @param string $table
     *
     * @return string
     */
    protected function searchPrimaryKeysFor($table)
    {
        return <<<SQL
select
    tc.table_schema, 
    tc.table_name, 
    tc.constraint_name
from information_schema.table_constraints as tc 
join information_schema.key_column_usage as kcu
on tc.constraint_name = kcu.constraint_name
join information_schema.constraint_column_usage as ccu
on ccu.constraint_name = tc.constraint_name
where true 
	and tc.constraint_type = 'PRIMARY KEY'
	and ccu.table_name = '{$table}'
	and true
group by tc.table_schema, tc.table_name, tc.constraint_name;
SQL;
    }

    /**
     * @param string $schema
     * @param string $table
     * @param string $name
     *
     * @return void
     */
    protected function dropPrimaryKeyIn($schema, $table, $name)
    {
        DB::unprepared("ALTER TABLE {$schema}.{$table} DROP CONSTRAINT {$name};");
    }

    /**
     * @param string $table
     */
    protected function dropPrimaryKeysFromTable($table)
    {
        $sql = $this->searchPrimaryKeysFor($table);

        $foreignKeys = DB::select($sql);

        foreach ($foreignKeys as $foreignKey) {
            $this->dropPrimaryKeyIn(
                $foreignKey->table_schema,
                $foreignKey->table_name,
                $foreignKey->constraint_name
            );
        }
    }
}
