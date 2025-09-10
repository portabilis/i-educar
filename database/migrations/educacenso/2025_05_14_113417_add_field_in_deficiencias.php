<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->smallInteger('transtorno_educacenso')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->dropColumn('transtorno_educacenso');
        });
    }
};
