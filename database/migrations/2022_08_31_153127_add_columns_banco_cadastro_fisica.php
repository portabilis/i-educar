<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsBancoCadastroFisica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->integer('ref_cod_banco')->nullable();
            $table->integer('agencia')->nullable();
            $table->integer('conta')->nullable();
            $table->integer('tipo_conta')->nullable();

            $table->foreign('ref_cod_banco')
            ->references('codigo')
            ->on('cadastro.banco');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->dropColumn('ref_cod_banco');
            $table->dropColumn('agencia');
            $table->dropColumn('conta');
            $table->dropColumn('tipo_conta');
        });
    }
}
