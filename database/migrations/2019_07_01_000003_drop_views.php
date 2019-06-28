<?php

use App\Support\Database\AsView;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropViews extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET search_path = cadastro, pg_catalog;
                
                DROP VIEW v_pessoa_fisica_simples;
                
                SET search_path = public, pg_catalog;
            '
        );

        $this->dropView('relatorio.view_dados_aluno');
        $this->createView('relatorio.view_dados_aluno');

        $this->dropView('relatorio.view_dados_escola');
        $this->createView('relatorio.view_dados_escola');
    }
}
