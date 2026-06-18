<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionGetDateInYear extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->down();

        $this->executeSqlFile(database_path('sqls/functions/pmieducar.get_date_in_year.sql'));
    }

    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS pmieducar.get_date_in_year(year INTEGER, date DATE)');
    }
}
