<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class CreateViewComponenteCurricularView extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->down();

        $this->createView('relatorio.view_componente_curricular');
    }

    public function down(): void
    {
        $this->dropView('relatorio.view_componente_curricular');
    }
}
