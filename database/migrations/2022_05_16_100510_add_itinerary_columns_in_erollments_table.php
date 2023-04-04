<?php

use Database\Custom\TypeIntergerArray;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $typeClass = new TypeIntergerArray(2);

        DB::connection()->setSchemaGrammar($typeClass);

        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->addColumn('int_array', 'tipo_itinerario')->nullable();
            $table->addColumn('int_array', 'composicao_itinerario')->nullable();
            $table->addColumn('integer', 'curso_itinerario')->nullable();
            $table->addColumn('boolean', 'itinerario_concomitante')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pmieducar.matricula_turma', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_itinerario',
                'composicao_itinerario',
                'curso_itinerario',
                'itinerario_concomitante'
            ]);
        });
    }
};
