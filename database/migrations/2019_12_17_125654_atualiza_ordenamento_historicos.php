<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AtualizaOrdenamentoHistoricos extends Migration
{
    use AsView;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_historico_9anos;');
        $this->dropView('relatorio.view_historico_series_anos;');
        $this->dropView('relatorio.view_historico_eja;');
        $this->dropView('relatorio.view_historico_eja_extra_curricular;');
        $this->dropView('relatorio.view_historico_series_anos_extra_curricular;');

        $this->createView('relatorio.view_historico_9_anos', '2019-09-17');
        $this->createView('relatorio.view_historico_series_anos', '2019-09-17');
        $this->createView('relatorio.view_historico_eja', '2019-09-17');
        $this->createView('relatorio.view_historico_eja_extra_curricular', '2019-09-17');
        $this->createView('relatorio.view_historico_series_anos_extra_curricular', '2019-09-17');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_historico_9anos;');
        $this->dropView('relatorio.view_historico_series_anos;');
        $this->dropView('relatorio.view_historico_eja;');
        $this->dropView('relatorio.view_historico_eja_extra_curricular;');
        $this->dropView('relatorio.view_historico_series_anos_extra_curricular;');

        $this->createView('relatorio.view_historico_9_anos', '2019-09-12');
        $this->createView('relatorio.view_historico_series_anos', '2019-09-12');
        $this->createView('relatorio.view_historico_eja', '2019-09-12');
        $this->createView('relatorio.view_historico_eja_extra_curricular', '2019-09-12');
        $this->createView('relatorio.view_historico_series_anos_extra_curricular', '2019-09-12');
    }
}
