<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class BatchMigrations extends Migration
{
    /**
     * @return array
     */
    private function getMigrations()
    {
        return file(database_path('lists/migrations.txt'));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('migrations')->truncate();

        foreach ($this->getMigrations() as $migration) {
            DB::table('migrations')->insert([
                'migration' => trim($migration),
                'batch' => 1,
            ]);
        }
    }
}
