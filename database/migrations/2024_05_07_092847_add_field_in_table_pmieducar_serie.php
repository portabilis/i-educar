<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->smallInteger('etapa_educacenso')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->dropColumn('etapa_educacenso');
        });
    }
};
