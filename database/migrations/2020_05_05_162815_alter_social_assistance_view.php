<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AlterSocialAssistanceView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-05-05.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_social_assistance-2020-05-05.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.exporter_social_assistance');
        $this->dropView('public.exporter_student');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_student-2020-04-22.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_social_assistance-2020-04-22.sql'
        );
    }
}
