<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInTipoLogradouroTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('tipo_logradouro');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.acervo_editora', function (Blueprint $table) {
            $table->foreign('ref_idtlog')
                ->references('idtlog')
                ->on('urbano.tipo_logradouro')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->foreign('idtlog')
                ->references('idtlog')
                ->on('urbano.tipo_logradouro');
        });

        Schema::table('pmieducar.instituicao', function (Blueprint $table) {
            $table->foreign('ref_idtlog')
                ->references('idtlog')
                ->on('urbano.tipo_logradouro')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
}
