<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('relatorio.view_situacao');
        $this->createView('relatorio.view_situacao');
    }

    public function down(): void
    {
        $this->dropView('relatorio.view_situacao');
    }
};
