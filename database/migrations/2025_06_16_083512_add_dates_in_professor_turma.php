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
        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->date('data_inicial')->nullable();
            $table->date('data_fim')->nullable();
        });

        $this->createView('public.educacenso_record50', '2025-06-16');
    }

    public function down(): void
    {
        $this->createView('public.educacenso_record50', '2025-06-13');

        Schema::table('modules.professor_turma', function (Blueprint $table) {
            $table->dropColumn('data_inicial');
            $table->dropColumn('data_fim');
        });
    }
};
