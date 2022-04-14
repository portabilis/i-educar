<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait MigrationUtils
{
    public function dropView($view)
    {
        DB::unprepared("DROP VIEW IF EXISTS {$view}");
    }

    public function dropMaterializedView($view)
    {
        DB::unprepared("DROP MATERIALIZED VIEW IF EXISTS {$view}");
    }

    public function refreshMaterializedView($view)
    {
        DB::unprepared("REFRESH MATERIALIZED VIEW {$view}");
    }

    public function executeSqlFile($filename)
    {
        DB::unprepared(
            file_get_contents($filename)
        );
    }
}
