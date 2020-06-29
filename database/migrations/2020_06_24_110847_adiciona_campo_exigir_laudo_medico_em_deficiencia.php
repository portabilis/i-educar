<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaCampoExigirLaudoMedicoEmDeficiencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->boolean('exigir_laudo_medico')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.deficiencia', function (Blueprint $table) {
            $table->dropColumn('exigir_laudo_medico');
        });
    }
}
