<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->boolean('utiliza_transporte_rural')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.aluno', function (Blueprint $table) {
            $table->dropColumn('utiliza_transporte_rural');
        });
    }
};
