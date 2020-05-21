<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AlteraViewSituacaoV2 extends Migration
{
    use AsView;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_situacao', '2020-04-06');
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
