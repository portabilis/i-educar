<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateEducacensoRecord60View extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.educacenso_record60');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.educacenso_record60-2020-04-13.sql'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('public.educacenso_record60');
    }
}
