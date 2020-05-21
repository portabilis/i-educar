<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalnearioRincaoToDistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.districts')->insert(
            [
                'city_id' => DB::table('public.cities')->where('ibge_code', '4220000')->first()->id,
                'name' => 'Balneário Rincão',
                'ibge_code' => '5',
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
        DB::table('public.districts')
            ->where('city_id', DB::table('public.cities')->where('ibge_code', '4220000')->first()->id)
            ->delete();
    }
}
