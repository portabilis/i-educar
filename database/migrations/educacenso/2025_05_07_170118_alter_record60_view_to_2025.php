<?php

use App\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up()
    {
        $this->dropView('public.educacenso_record60');

        $this->executeSqlFile(
            database_path('sqls/views/public.educacenso_record60-2025-05-07.sql')
        );
    }

    public function down()
    {
        $this->dropView('public.educacenso_record60');

        $this->executeSqlFile(
            database_path('sqls/views/public.educacenso_record60-2024-05-20.sql')
        );
    }
};
