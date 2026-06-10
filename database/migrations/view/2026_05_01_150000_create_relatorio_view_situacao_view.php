<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioViewSituacaoView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('relatorio.view_situacao');
    }

    public function down(): void
    {
        $this->dropView('relatorio.view_situacao');
    }
}
