<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDescricaoOnSerieTable extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->string('descricao', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->dropColumn('descricao');
        });
    }
}
