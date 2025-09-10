<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->smallInteger('qtd_agronomos_horticultores')->nullable();
            $table->smallInteger('qtd_revisor_braile')->nullable();
            $table->smallInteger('acao_area_ambiental')->nullable();
        });

        DB::statement('ALTER TABLE IF EXISTS pmieducar.escola ADD COLUMN acoes_area_ambiental smallint[];');
    }

    public function down(): void
    {
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('qtd_agronomos_horticultores');
            $table->dropColumn('qtd_revisor_braile');
            $table->dropColumn('acao_area_ambiental');
            $table->dropColumn('acoes_area_ambiental');
        });
    }
};
