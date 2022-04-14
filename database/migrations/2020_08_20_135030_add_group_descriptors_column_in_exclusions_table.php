<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupDescriptorsColumnInExclusionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.area_conhecimento_excluidos', function (Blueprint $table) {
            $table->boolean('agrupar_descritores')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules.area_conhecimento_excluidos', function (Blueprint $table) {
            $table->dropColumn('agrupar_descritores');
        });
    }
}
