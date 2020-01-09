<?php

use App\Support\Database\DropForeignKey;
use App\Support\Database\EnableDisableForeignKeys;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSomeForeignKeysInCadastroDocumentoTable extends Migration
{
    use DropForeignKey, EnableDisableForeignKeys;

    /**
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->disableForeignKeys();

        $this->dropForeignKeysIn('documento');

        Schema::table('cadastro.documento', function (Blueprint $table) {
            $table->foreign('idpes_rev')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idpes_cad')
                ->references('idpes')
                ->on('cadastro.pessoa')
                ->onDelete('set null');

            $table->foreign('idorg_exp_rg')
                ->references('idorg_rg')
                ->on('cadastro.orgao_emissor_rg')
                ->onDelete('restrict');

            $table->foreign('idpes')
                ->references('idpes')
                ->on('cadastro.fisica');

            $table->foreign('cartorio_cert_civil_inep')
                ->references('id')
                ->on('cadastro.codigo_cartorio_inep')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        $this->enableForeignKeys();
    }
}
