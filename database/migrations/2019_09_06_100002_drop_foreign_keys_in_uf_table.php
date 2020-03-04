<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInUfTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysFromTable('uf');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.acervo_editora', function (Blueprint $table) {
            $table->foreign('ref_sigla_uf')
                ->references('sigla_uf')
                ->on('uf')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->foreign('sigla_uf_exp_rg')
                ->references('sigla_uf')
                ->on('uf');

            $table->foreign('sigla_uf_cert_civil')
                ->references('sigla_uf')
                ->on('uf');

            $table->foreign('sigla_uf_cart_trabalho')
                ->references('sigla_uf')
                ->on('uf');
        });

        Schema::table('cadastro.endereco_externo', function (Blueprint $table) {
            $table->foreign('sigla_uf')
                ->references('sigla_uf')
                ->on('uf');
        });

        Schema::table('public.municipio', function (Blueprint $table) {
            $table->foreign('sigla_uf')
                ->references('sigla_uf')
                ->on('uf');
        });

        Schema::table('cadastro.codigo_cartorio_inep', function (Blueprint $table) {
            $table->foreign('ref_sigla_uf')
                ->references('sigla_uf')
                ->on('uf');
        });
    }
}
