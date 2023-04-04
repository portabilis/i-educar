<?php

use App\Models\LegacyStudent;
use Illuminate\Database\Migrations\Migration;

class FixActiveStudentsWithExclusionDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        LegacyStudent::where('ativo', 1)
            ->update(['data_exclusao' => null]);
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
