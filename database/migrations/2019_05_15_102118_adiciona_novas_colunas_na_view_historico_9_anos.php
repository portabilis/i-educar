<?php

use App\Support\Database\AsView;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaNovasColunasNaViewHistorico9Anos extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_historico_escolar_9_anos', '2019-05-17');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_historico_escolar_9_anos');
        $this->createView('relatorio.view_historico_escolar_9_anos', '2019-01-01');
    }
}
