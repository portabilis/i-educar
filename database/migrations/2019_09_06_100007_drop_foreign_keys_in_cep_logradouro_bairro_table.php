<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInCepLogradouroBairroTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('cep_logradouro_bairro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastro.endereco_pessoa', function (Blueprint $table) {
            $table->foreign(['cep', 'idbai', 'idlog'])
                ->references(['cep', 'idbai', 'idlog'])
                ->on('urbano.cep_logradouro_bairro')
                ->onUpdate('cascade');
        });

        Schema::table('modules.ponto_transporte_escolar', function (Blueprint $table) {
            $table->foreign(['idbai', 'idlog', 'cep'])
                ->references(['idbai', 'idlog', 'cep'])
                ->on('urbano.cep_logradouro_bairro');
        });
    }
}
