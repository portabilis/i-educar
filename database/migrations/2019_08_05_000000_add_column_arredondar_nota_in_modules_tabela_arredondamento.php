<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnArredondarNotaInModulesTabelaArredondamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.tabela_arredondamento', function (Blueprint $table) {
            $table->smallInteger('arredondar_nota')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.tabela_arredondamento', function (Blueprint $table) {
            $table->dropColumn('arredondar_nota');
        });
    }
}
