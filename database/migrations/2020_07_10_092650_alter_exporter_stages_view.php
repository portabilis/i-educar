<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AlterExporterStagesView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_stages');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_stages-2020-07-10.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_stages');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_stages-2020-07-09.sql'
        );
    }
}
