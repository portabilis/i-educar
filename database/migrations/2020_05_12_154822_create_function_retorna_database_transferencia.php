<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateFunctionRetornaDatabaseTransferencia extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->executeSqlFile(__DIR__ . '/../sqls/views/pmieducar.retorna_database_transferencia-2020-05-12.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP FUNCTION IF EXISTS pmieducar.retorna_database_transferencia(ano INTEGER, instituicao_id INTEGER)');
    }
}
