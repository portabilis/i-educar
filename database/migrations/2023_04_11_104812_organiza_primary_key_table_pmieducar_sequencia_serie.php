<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.sequencia_serie DROP CONSTRAINT IF EXISTS sequencia_serie_pkey;');
        Schema::table('pmieducar.sequencia_serie', static function (Blueprint $table) {
            $table->increments('id');
            $table->unique([
                'ref_serie_origem',
                'ref_serie_destino',
            ]);
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pmieducar.sequencia_serie', static function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->dropUnique([
                'ref_serie_origem',
                'ref_serie_destino',
            ]);
            $table->dropColumn('id');
        });

        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.sequencia_serie ADD CONSTRAINT sequencia_serie_pkey PRIMARY KEY (ref_serie_origem, ref_serie_destino);');
    }
};
