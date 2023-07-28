<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Menu::where('process', '9998845')->update(['link' => '/educacenso/export-situation']);
    }

    public function down(): void
    {
        Menu::where('process', '9998845')->update(['link' => '/intranet/educar_exportacao_educacenso.php?fase2=1']);
    }
};
