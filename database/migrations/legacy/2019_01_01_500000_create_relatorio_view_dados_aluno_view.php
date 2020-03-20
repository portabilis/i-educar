<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioViewDadosAlunoView extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_dados_aluno');
        $this->createView('relatorio.view_dados_aluno', '2019-09-06');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('relatorio.view_dados_aluno');
    }
}
