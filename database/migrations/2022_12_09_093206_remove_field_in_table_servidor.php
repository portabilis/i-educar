<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.servidor DROP CONSTRAINT IF EXISTS servidor_ref_cod_subnivel_;');
        Schema::table('pmieducar.servidor', function (Blueprint $table) {
            $table->dropColumn('ref_cod_subnivel');
        });
    }
};
