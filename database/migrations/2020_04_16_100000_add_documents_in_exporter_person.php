<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AddDocumentsInExporterPerson extends Migration
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
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_person-2020-04-16.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-03.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_teacher-2020-04-07.sql'
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
        $this->dropView('public.exporter_teacher');
        $this->dropView('public.exporter_person');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_person-2020-04-01.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-03.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_teacher-2020-04-07.sql'
        );
    }
}
