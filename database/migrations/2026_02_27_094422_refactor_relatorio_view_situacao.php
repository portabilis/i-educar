<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->createView('relatorio.view_situacao', '2026-02-24');
    }

    public function down(): void
    {
        $this->createView('relatorio.view_situacao', '2020-04-06');
    }
};
