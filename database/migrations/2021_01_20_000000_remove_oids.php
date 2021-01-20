<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveOids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = DB::select(
            "select schemaname || '.' || tablename as table_name from pg_tables WHERE schemaname <> 'pg_catalog' AND schemaname <> 'information_schema';"
        );

        foreach ($tables as $table) {
            DB::unprepared("alter table {$table->table_name} SET WITHOUT oids;");
        }
    }
}
