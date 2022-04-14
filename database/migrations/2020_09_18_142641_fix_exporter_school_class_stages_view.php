<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class FixExporterSchoolClassStagesView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_school_class_stages-2020-09-18.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_school_class_stages-2020-07-09.sql'
        );
    }
}
