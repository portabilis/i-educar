<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupDescriptorsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules.area_conhecimento', function (Blueprint $table) {
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
        Schema::table('modules.area_conhecimento', function (Blueprint $table) {
            $table->dropColumn('agrupar_descritores');
        });
    }
}
