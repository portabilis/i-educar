<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class RecreateRelatorioViewDadosEscolaView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_dados_escola');
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
