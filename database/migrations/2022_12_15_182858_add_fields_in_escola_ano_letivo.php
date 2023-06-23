<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.escola_ano_letivo', function (Blueprint $table) {
            $table->addColumn('boolean', 'copia_dados_professor')->default(false);
            $table->addColumn('boolean', 'copia_dados_demais_servidores')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('escola_ano_letivo', function (Blueprint $table) {
            $table->dropColumn([
                'copia_dados_professor',
                'copia_dados_demais_servidores',
            ]);
        });
    }
};
