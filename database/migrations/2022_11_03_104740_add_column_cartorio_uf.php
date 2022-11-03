<?php

use Illuminate\Database\Migrations\Migration;
use App\Support\Database\MigrationUtils;

class AddColumnCartorioUf extends Migration
{
    use MigrationUtils;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_responsavel');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_responsavel.sql'
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
