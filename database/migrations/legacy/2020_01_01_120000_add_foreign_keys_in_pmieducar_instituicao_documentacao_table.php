<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysInPmieducarInstituicaoDocumentacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.instituicao_documentacao', function (Blueprint $table) {
            $table->foreign('instituicao_id')
                ->references('cod_instituicao')
                ->on('pmieducar.instituicao')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.instituicao_documentacao', function (Blueprint $table) {
            $table->dropForeign(['instituicao_id']);
        });
    }
}
