<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FillTimestampsToUserType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'UPDATE pmieducar.tipo_usuario SET created_at = data_cadastro;'
        );

        DB::table('pmieducar.tipo_usuario')->update([
            'updated_at' => now(),
        ]);
    }
}
