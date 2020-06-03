<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class Upgrade extends Migration
{
    /**
     * @var array
     */
    protected $files = [
        __DIR__ . '/../upgrade2.3.txt',
        __DIR__ . '/../../ieducar/modules/Reports/database/upgrade2.3.txt',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $counter = 1;

        foreach ($this->files as $file) {
            $migrations = file($file, FILE_SKIP_EMPTY_LINES);

            foreach ($migrations as $migration) {
                DB::table('public.migrations')->insert([
                    'migration' => $migration,
                    'batch' => $counter++,
                ]);
            }
        }
    }
}
