<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola_ano_letivo', function (Blueprint $table) {
            $table->addColumn('boolean', 'copia_turmas')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.escola_ano_letivo', function (Blueprint $table) {
            $table->dropColumn('copia_turmas');
        });
    }
};
