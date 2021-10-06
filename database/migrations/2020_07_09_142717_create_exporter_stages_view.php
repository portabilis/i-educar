<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateExporterStagesView extends Migration
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
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_school_stages-2020-07-09.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_school_class_stages-2020-07-09.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_stages-2020-07-09.sql'
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
        $this->dropView('public.exporter_school_stages');
        $this->dropView('public.exporter_school_class_stages');
    }
}
