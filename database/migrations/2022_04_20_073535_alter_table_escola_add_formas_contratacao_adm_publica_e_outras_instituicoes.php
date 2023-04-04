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

        Schema::table(
            'pmieducar.escola',
            static fn (Blueprint $table) =>
            $table
                ->addColumn('int_array', 'formas_contratacao_adm_publica_e_outras_instituicoes')
                ->nullable()
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'pmieducar.escola',
            static fn (Blueprint $table) => $table->dropColumn('formas_contratacao_adm_publica_e_outras_instituicoes')
        );
    }
};
