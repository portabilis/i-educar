<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait AsView
{
    /**
     * Create a view from SQL file.
     *
     * @param string      $view
     * @param string|null $version
     *
     * @return void
     */
    public function createView($view, $version = null)
    {
        if ($version) {
            $view = "{$view}-{$version}";
        }

        DB::unprepared(
            file_get_contents(database_path("sqls/views/{$view}.sql"))
        );
    }

    /**
     * Drop a view if exists.
     *
     * @param string $view
     *
     * @return void
     */
    public function dropView($view)
    {
        DB::unprepared(
            "DROP VIEW IF EXISTS {$view};"
        );
    }
}
