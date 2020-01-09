<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait EnableDisableForeignKeys
{
    /**
     * Disable foreign keys check.
     *
     * @return void
     */
    protected function disableForeignKeys()
    {
        DB::statement('SET session_replication_role = replica;');
    }

    /**
     * Enable foreign keys check.
     *
     * @return void
     */
    protected function enableForeignKeys()
    {
        DB::statement('SET session_replication_role = origin;');
    }
}
