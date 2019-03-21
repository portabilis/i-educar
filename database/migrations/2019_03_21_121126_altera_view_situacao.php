<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class AlteraViewSituacao extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createView('relatorio.view_situacao', '2019-03-21');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_situacao');
        $this->createView('relatorio.view_situacao', '2019-02-28');
    }
}
