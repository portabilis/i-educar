<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.educacenso_record60');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.educacenso_record60-2023-05-17.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.educacenso_record60');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.educacenso_record60-2022-07-26.sql'
        );
    }
};
