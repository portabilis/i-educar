<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->boolean('bloquear_troca_de_situacao')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula', function (Blueprint $table) {
            $table->dropColumn('bloquear_troca_de_situacao');
        });
    }
};
