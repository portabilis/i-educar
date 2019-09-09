<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait IncrementSequence
{
    /**
     * @param string $table
     * @param string $column
     *
     * @return void
     */
    public function incrementSequence($table, $column = 'id')
    {
        if (class_exists($table)) {
            $class = new $table;

            $table = $class->getTable();
            $column = $class->getKeyName();
        }

        DB::unprepared(
            "
                SELECT setval(pg_get_serial_sequence('{$table}', '{$column}'), coalesce(max({$column}), 1)) FROM {$table};
            "
        );
    }
}
