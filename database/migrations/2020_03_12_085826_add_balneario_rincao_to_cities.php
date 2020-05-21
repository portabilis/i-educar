<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalnearioRincaoToCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.cities')->insert(
            [
                'state_id' => DB::table('public.states')->where('abbreviation', 'SC')->first()->id,
                'name' => 'Balneário Rincão',
                'ibge_code' => '4220000',
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()')
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('public.cities')
            ->where('ibge_code', '4220000')
            ->delete();
    }
}
