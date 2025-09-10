<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->renameColumn('estrutura_curricular', 'organizacao_curricular');
        });

        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record60');

        $this->createView('public.educacenso_record20', '2025-06-11');
        $this->createView('public.educacenso_record50', '2025-06-11');
        $this->createView('public.educacenso_record60', '2025-06-11');
    }

    public function down(): void
    {
        $this->dropView('public.educacenso_record20');
        $this->dropView('public.educacenso_record50');
        $this->dropView('public.educacenso_record60');

        $this->createView('public.educacenso_record20', '2025-06-09');
        $this->createView('public.educacenso_record50', '2024-05-23');
        $this->createView('public.educacenso_record60', '2025-06-06');

        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->renameColumn('organizacao_curricular', 'estrutura_curricular');
        });
    }
};
