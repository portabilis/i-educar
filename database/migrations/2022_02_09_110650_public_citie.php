<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;

class PublicCitie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        City::where('id', 3611)
            ->update(['name' => 'Mogi das Cruzes']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
