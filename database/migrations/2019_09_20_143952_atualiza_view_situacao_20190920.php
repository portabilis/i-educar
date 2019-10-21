<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AtualizaViewSituacao20190920 extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_situacao', '2019-09-20');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_situacao');
        $this->createView('relatorio.view_situacao', '2019-03-21');
    }
}
