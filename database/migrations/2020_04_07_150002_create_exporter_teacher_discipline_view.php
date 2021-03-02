<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateExporterTeacherDisciplineView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_teacher_disciplines');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_teacher_disciplines-2020-04-07.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_teacher_disciplines');
    }
}
