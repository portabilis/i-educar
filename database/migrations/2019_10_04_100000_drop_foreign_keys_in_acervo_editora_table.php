<?php

use App\Support\Database\DropForeignKey;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeysInAcervoEditoraTable extends Migration
{
    use DropForeignKey;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropForeignKeysIn('acervo_editora');

        Schema::table('pmieducar.acervo_editora', function (Blueprint $table) {
            $table->foreign('ref_cod_biblioteca')
                ->references('cod_biblioteca')
                ->on('pmieducar.biblioteca')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
}
