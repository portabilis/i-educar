<?php

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;

class MigrateCoutryIdInStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $brazil = Country::query()->where('ibge_code', 76)->first();

        if (empty($brazil)) {
            return;
        }

        State::query()->whereIn('ibge_code', [
            12, 27, 13, 16, 29, 23, 53, 32, 52, 21, 31, 50, 51, 15, 25, 26, 22, 41, 33, 24, 11, 14, 43, 42, 28, 35, 17,
        ])->update([
            'country_id' => $brazil->getKey(),
        ]);
    }
}
