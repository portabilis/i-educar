<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreatePublicExporterStudentView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_student');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-01.sql'
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
    }
}
