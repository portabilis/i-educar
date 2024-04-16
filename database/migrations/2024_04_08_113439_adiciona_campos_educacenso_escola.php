<?php

use Database\Custom\TypeIntergerArray;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        $typeClass = new TypeIntergerArray(2);
        DB::connection()->setSchemaGrammar($typeClass);

        Schema::table(
            'pmieducar.escola',
            static fn (Blueprint $table) => $table
                ->addColumn('int_array', 'acoes_area_ambiental')
                ->nullable()
        );
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
