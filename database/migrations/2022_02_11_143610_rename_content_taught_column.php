<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameContentTaughtColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.conteudo_ministrado', function(Blueprint $table) {
            $table->renameColumn('procedimento_metodologico', 'atividades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.conteudo_ministrado', function(Blueprint $table) {
            $table->renameColumn('atividades', 'procedimento_metodologico');
        });
    }
}
