<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateViewDadosEscola extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_dados_escola');
        $this->createView('relatorio.view_dados_escola', '2020-08-27');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_dados_escola');
    }
}
