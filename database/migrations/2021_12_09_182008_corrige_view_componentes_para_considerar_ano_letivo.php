<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CorrigeViewComponentesParaConsiderarAnoLetivo extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_componente_curricular');
        $this->createView('relatorio.view_componente_curricular', '2021-12-09');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_componente_curricular');
        $this->createView('relatorio.view_componente_curricular', '2021-03-24');
    }
}
