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
    }

    public function down(): void
    {
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->renameColumn('organizacao_curricular', 'estrutura_curricular');
        });
    }
};
