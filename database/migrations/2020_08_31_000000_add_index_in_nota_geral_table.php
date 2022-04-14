<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInNotaGeralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.nota_geral', function (Blueprint $table) {
            $table->index(['nota_aluno_id']);
            $table->index(['etapa']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.nota_geral', function (Blueprint $table) {
            $table->dropIndex(['nota_aluno_id']);
            $table->dropIndex(['etapa']);
        });
    }
}
