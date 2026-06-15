<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('modules.tabela_arredondamento', function (Blueprint $table) {
            $table->boolean('normalizar_media')->default(false);
        });
    }

    public function down()
    {
        Schema::table('modules.tabela_arredondamento', function (Blueprint $table) {
            $table->dropColumn('normalizar_media');
        });
    }
};
