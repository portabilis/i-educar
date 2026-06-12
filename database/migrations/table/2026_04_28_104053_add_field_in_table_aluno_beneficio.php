<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.aluno_beneficio', function (Blueprint $table) {
            $table->boolean('bolsa_familia')->default(false);
        });
    }

    public function down()
    {
        Schema::table('pmieducar.aluno_beneficio', function (Blueprint $table) {
            $table->dropColumn('bolsa_familia');
        });
    }
};
