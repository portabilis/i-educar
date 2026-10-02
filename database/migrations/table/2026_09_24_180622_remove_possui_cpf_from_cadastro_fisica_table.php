<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropColumn('possui_cpf');
        });
    }

    public function down(): void
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->boolean('possui_cpf')->default(true);
        });

        DB::statement('UPDATE cadastro.fisica SET possui_cpf = false WHERE COALESCE(cpf, 0) = 0');
    }
};
