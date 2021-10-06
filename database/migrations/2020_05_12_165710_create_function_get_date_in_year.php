<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionGetDateInYear extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->executeSqlFile(__DIR__ . '/../sqls/views/pmieducar.get_date_in_year-2020-05-12.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP FUNCTION IF EXISTS pmieducar.get_date_in_year(year INTEGER, date DATE)');
    }
}
