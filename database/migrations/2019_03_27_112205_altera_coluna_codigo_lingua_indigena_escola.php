<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AlteraColunaCodigoLinguaIndigenaEscola extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pmieducar.escola DROP CONSTRAINT IF EXISTS escola_codigo_indigena_fk;');

        DB::statement('alter table pmieducar.escola alter column codigo_lingua_indigena type integer[] USING array[codigo_lingua_indigena]::integer[]');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table pmieducar.escola alter column codigo_lingua_indigena type integer USING codigo_lingua_indigena[1]::integer');

        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->foreign('codigo_lingua_indigena', 'escola_codigo_indigena_fk')
                ->references('id')
                ->on('modules.lingua_indigena_educacenso');
        });
    }
}
