<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::table('pmieducar.serie', function (Blueprint $table) {
            $table->index('ref_cod_curso');
        });
    }

    public function down()
    {
        DB::unprepared('DROP INDEX IF EXISTS pmieducar.pmieducar_serie_ref_cod_curso_index;');
    }
};
