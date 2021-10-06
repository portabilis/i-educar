<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AlterFuncionGetDateInYear extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->executeSqlFile(__DIR__ . '/../sqls/functions/pmieducar.get_date_in_year-2020-07-03.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->executeSqlFile(__DIR__ . '/../sqls/functions/pmieducar.get_date_in_year.sql');
    }
}
