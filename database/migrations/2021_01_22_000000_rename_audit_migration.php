<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameAuditMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $batch = DB::table('migrations')->max('batch') + 1;

        DB::table('migrations')->insert([
            'batch' => $batch,
            'migration' => '2020_01_01_000003_audit_functions',
        ]);

        DB::table('migrations')->insert([
            'batch' => $batch,
            'migration' => '2020_01_01_000004_audit_triggers',
        ]);
    }
}
