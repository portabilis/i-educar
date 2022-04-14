<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateExporterSocialAssistanceView extends Migration
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

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_social_assistance-2020-04-24.sql'
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
    }
}
