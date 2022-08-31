<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsTableConteudoMinistradoAee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->dropColumn(['frequencia_aee_id']);
            $table->dropColumn(['data_cadastro']);
            $table->dropColumn(['data_atualizacao']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.conteudo_ministrado_aee', function (Blueprint $table) {
            $table->dropColumn(['frequencia_aee_id']);
            $table->dropColumn(['data_cadastro']);
            $table->dropColumn(['data_atualizacao']);
        });
    }
}
