<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->timestamp('data_recebimento')->nullable();
            $table->integer('ref_usuario_recebimento')->nullable();
            $table->foreign('ref_usuario_recebimento', 'transfer_receipt_user_fk')
                ->references('cod_usuario')->on('pmieducar.usuario');
        });
    }

    public function down(): void
    {
        Schema::table('pmieducar.transferencia_solicitacao', function (Blueprint $table) {
            $table->dropForeign('transfer_receipt_user_fk');
            $table->dropColumn(['data_recebimento', 'ref_usuario_recebimento']);
        });
    }
};
