<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateViewDadosEscolaView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->down();

        $this->createView('relatorio.view_dados_escola');
    }

    public function down(): void
    {
        $this->dropView('relatorio.view_dados_escola');
    }
}
