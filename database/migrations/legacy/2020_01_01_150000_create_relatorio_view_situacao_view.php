<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioViewSituacaoView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_situacao');
        $this->createView('relatorio.view_situacao');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_situacao');
    }
}
