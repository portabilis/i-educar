<?php

use Illuminate\Database\Migrations\Migration;
use App\Support\Database\MigrationUtils;

class StudentViewEdit extends Migration
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
            __DIR__ . '/../sqls/views/public.exporter_student-2021-07-19.sql'
        );

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_social_assistance-2020-05-07.sql'
        );
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       
    }
}
