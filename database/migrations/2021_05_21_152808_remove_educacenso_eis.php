<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveEducacensoEis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            DELETE FROM modules.educacenso_ies  WHERE ies_id IN (
                302,722,723,839,840,891,1066,1124,1212,1226,1437,1442,1668,1692,1706,1707,1731,1767,1858,2146,2168,2243,
                2245,2791,2794,2891,2974,3776,3784,3788,4631,5066,5216,5317,12847,14002,18290,18642,18714,18716,19049,
                19050,19208,19332,19342,19375,19405,19733,19735,20612,21421,21614,21676,21932,22126,22127,22129,22134,
                22135,22136,22140,22143,22149,22150,22151,22152,22153,22157,22169,22170,22225,22226,22227,22228,22229,
                22235
            )
        ');
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
