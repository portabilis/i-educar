<?php

use Illuminate\Database\Migrations\Migration;
use App\Support\Database\MigrationUtils;

class UpdateMatriculaAtivo extends Migration
{   
    use MigrationUtils;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.exporter_responsaveis_turma');

        $this->executeSqlFile(
            __DIR__ . '/../sqls/views/public.exporter_responsavel_turma.sql'
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

