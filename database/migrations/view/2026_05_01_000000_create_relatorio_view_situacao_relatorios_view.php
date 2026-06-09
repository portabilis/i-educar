<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioViewSituacaoRelatoriosView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('relatorio.view_situacao_relatorios');
        $this->createView('relatorio.view_situacao_relatorios');
    }

    public function down(): void
    {
        $this->dropView('relatorio.view_situacao_relatorios');
    }
}
