<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CriaViewSituacaoRelatorios extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_situacao_relatorios', '2020-04-06');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_situacao_relatorios');
    }
}
