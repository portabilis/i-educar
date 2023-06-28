<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.turma DROP CONSTRAINT IF EXISTS turma_ref_cod_infra_predio_comodo_fkey;');
        Schema::table('pmieducar.turma', function (Blueprint $table) {
            $table->dropColumn('ref_cod_infra_predio_comodo');
        });
    }
};
