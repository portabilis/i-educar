<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInExporterStudent extends Migration
{
    use \App\Support\Database\MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_student');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-22.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_student');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-17.sql'
        );
    }
}
