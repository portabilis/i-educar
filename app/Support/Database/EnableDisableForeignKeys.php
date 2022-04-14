<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait EnableDisableForeignKeys
{
    /**
     * Disable foreign keys check.
     *
     * @param string $table
     *
     * @return void
     */
    protected function disableForeignKeys($table)
    {
        DB::statement("ALTER TABLE {$table} DISABLE TRIGGER ALL;");
    }

    /**
     * Enable foreign keys check.
     *
     * @param string $table
     *
     * @return void
     */
    protected function enableForeignKeys($table)
    {
        DB::statement("ALTER TABLE {$table} ENABLE TRIGGER ALL;");
    }
}
