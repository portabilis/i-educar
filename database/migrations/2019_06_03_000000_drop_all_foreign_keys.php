<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropAllForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $database = DB::selectOne('select current_database() as database')->database;

        $data = DB::select(
            "
                SELECT
                    table_schema,
                    table_name,
                    constraint_name 
                from information_schema.table_constraints
                where true 
                and constraint_type = 'FOREIGN KEY'
                and table_catalog = '{$database}'
                and table_schema in (
                    'cadastro', 'modules', 'pmicontrolesis', 'pmieducar', 'portal', 'public', 'relatorio', 'urbano'
                );
            "
        );

        foreach ($data as $item) {
            DB::unprepared(
                "ALTER TABLE {$item->table_schema}.{$item->table_name} DROP CONSTRAINT IF EXISTS {$item->constraint_name} CASCADE;"
            );
        }
    }
}
