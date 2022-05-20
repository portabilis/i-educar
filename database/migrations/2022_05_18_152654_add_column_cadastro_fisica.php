<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCadastroFisica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cadastro.fisica', function (Blueprint $table) {
            $table->integer('ref_cod_profissao')->nullable();

            $table->foreign('ref_cod_profissao')
                   ->references('cod_profissao')
                   ->on('cadastro.profissao');
                   
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
            $table->dropColumn('ref_cod_profissao');
        });
    }
}
