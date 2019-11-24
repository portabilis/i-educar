<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait DropForeignKey
{
    /**
     * @param string $table
     *
     * @return string
     */
    protected function searchForeignKeysIn($table)
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
	and tc.constraint_type = 'FOREIGN KEY'
	and tc.table_name = '{$table}'
group by tc.table_schema, tc.table_name, tc.constraint_name;
SQL;
    }

    /**
     * @param string $table
     *
     * @return string
     */
    protected function searchForeignKeysThatReferences($table)
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
	and tc.constraint_type = 'FOREIGN KEY'
	and ccu.table_name = '{$table}'
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
    protected function dropForeignKeyIn($schema, $table, $name)
    {
        DB::unprepared("ALTER TABLE {$schema}.{$table} DROP CONSTRAINT {$name};");
    }

    /**
     * @deprecated
     *
     * @see DropForeignKey::dropForeignKeysThatReferences()
     *
     * @param string $table
     */
    protected function dropForeignKeysFromTable($table)
    {
        $this->dropForeignKeysThatReferences($table);
    }

    /**
     * @param string $table
     */
    protected function dropForeignKeysIn($table)
    {
        $sql = $this->searchForeignKeysIn($table);

        $foreignKeys = DB::select($sql);

        foreach ($foreignKeys as $foreignKey) {
            $this->dropForeignKeyIn(
                $foreignKey->table_schema,
                $foreignKey->table_name,
                $foreignKey->constraint_name
            );
        }
    }

    /**
     * @param string $table
     */
    protected function dropForeignKeysThatReferences($table)
    {
        $sql = $this->searchForeignKeysThatReferences($table);

        $foreignKeys = DB::select($sql);

        foreach ($foreignKeys as $foreignKey) {
            $this->dropForeignKeyIn(
                $foreignKey->table_schema,
                $foreignKey->table_name,
                $foreignKey->constraint_name
            );
        }
    }
}
