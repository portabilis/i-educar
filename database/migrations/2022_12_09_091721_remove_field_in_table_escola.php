<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.escola DROP CONSTRAINT IF EXISTS escola_ref_cod_escola_rede_ensino_fkey;');
        Schema::table('pmieducar.escola', function (Blueprint $table) {
            $table->dropColumn('ref_cod_escola_rede_ensino');
        });
    }
};
