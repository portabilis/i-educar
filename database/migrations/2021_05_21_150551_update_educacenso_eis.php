<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateEducacensoEis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE public.employee_graduations
            SET college_id = (
                CASE college_id
                    WHEN 302 THEN 19257
                    WHEN 722 THEN 721
                    WHEN 723 THEN 721
                    WHEN 839 THEN 1498
                    WHEN 840 THEN 1498
                    WHEN 891 THEN 1818
                    WHEN 1066 THEN 2148
                    WHEN 1124 THEN 3588
                    WHEN 1212 THEN 721
                    WHEN 1226 THEN 2132
                    WHEN 1437 THEN 707
                    WHEN 1442 THEN 1587
                    WHEN 1668 THEN 1818
                    WHEN 1692 THEN 707
                    WHEN 1706 THEN 2566
                    WHEN 1707 THEN 2566
                    WHEN 1731 THEN 2973
                    WHEN 1767 THEN 1587
                    WHEN 1858 THEN 3588
                    WHEN 2146 THEN 2132
                    WHEN 2168 THEN 2149
                    WHEN 2243 THEN 1462
                    WHEN 2245 THEN 1498
                    WHEN 2791 THEN 2973
                    WHEN 2794 THEN 781
                    WHEN 2891 THEN 3186
                    WHEN 2974 THEN 2973
                    WHEN 3776 THEN 1996
                    WHEN 3784 THEN 2908
                    WHEN 3788 THEN 2241
                    WHEN 4631 THEN 2082
                    WHEN 5066 THEN 13684
                    WHEN 5216 THEN 4655
                    WHEN 5317 THEN 448
                    WHEN 12847 THEN 1657
                    WHEN 14002 THEN 1805
                    WHEN 18290 THEN 17632
                    WHEN 18642 THEN 383
                    WHEN 18714 THEN 18147
                    WHEN 18716 THEN 17632
                    WHEN 19049 THEN 18979
                    WHEN 19050 THEN 2537
                    WHEN 19208 THEN 2537
                    WHEN 19332 THEN 13982
                    WHEN 19342 THEN 4135
                    WHEN 19375 THEN 17632
                    WHEN 19405 THEN 17632
                    WHEN 19733 THEN 18147
                    WHEN 19735 THEN 17632
                    WHEN 20612 THEN 2885
                    WHEN 21421 THEN 1055
                    WHEN 21614 THEN 17632
                    WHEN 21676 THEN 1988
                    WHEN 21932 THEN 21931
                    WHEN 22126 THEN 19323
                    WHEN 22127 THEN 19260
                    WHEN 22129 THEN 19781
                    WHEN 22134 THEN 19786
                    WHEN 22135 THEN 20587
                    WHEN 22136 THEN 21687
                    WHEN 22140 THEN 20588
                    WHEN 22143 THEN 21238
                    WHEN 22149 THEN 21693
                    WHEN 22150 THEN 19785
                    WHEN 22151 THEN 21552
                    WHEN 22152 THEN 21553
                    WHEN 22153 THEN 19780
                    WHEN 22157 THEN 21886
                    WHEN 22169 THEN 21834
                    WHEN 22170 THEN 21833
                    WHEN 22225 THEN 19298
                    WHEN 22226 THEN 19783
                    WHEN 22227 THEN 21280
                    WHEN 22228 THEN 21900
                    WHEN 22229 THEN 21554
                    WHEN 22235 THEN 21903
                END
            )
            WHERE college_id in (
                 302,722,723,839,840,891,1066,1124,1212,1226,1437,1442,1668,1692,1706,1707,1731,1767,
                 1858,2146,2168,2243,2245,2791,2794,2891,2974,3776,3784,3788,4631,5066,5216,5317,12847,
                 14002,18290,18642,18714,18716,19049,19050,19208,19332,19342,19375,19405,19733,19735,
                 20612,21421,21614,21676,21932,22126,22127,22129,22134,22135,22136,22140,22143,22149,
                 22150,22151,22152,22153,22157,22169,22170,22225,22226,22227,22228,22229,22235
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            UPDATE public.employee_graduations
            SET college_id = (
                CASE college_id
                    WHEN 19257 THEN 302
                    WHEN 721 THEN 722
                    WHEN 721 THEN 723
                    WHEN 1498 THEN 839
                    WHEN 1498 THEN 840
                    WHEN 1818 THEN 891
                    WHEN 2148 THEN 1066
                    WHEN 3588 THEN 1124
                    WHEN 721 THEN 1212
                    WHEN 2132 THEN 1226
                    WHEN 707 THEN 1437
                    WHEN 1587 THEN 1442
                    WHEN 1818 THEN 1668
                    WHEN 707 THEN 1692
                    WHEN 2566 THEN 1706
                    WHEN 2566 THEN 1707
                    WHEN 2973 THEN 1731
                    WHEN 1587 THEN 1767
                    WHEN 3588 THEN 1858
                    WHEN 2132 THEN 2146
                    WHEN 2149 THEN 2168
                    WHEN 1462 THEN 2243
                    WHEN 1498 THEN 2245
                    WHEN 2973 THEN 2791
                    WHEN 781 THEN 2794
                    WHEN 3186 THEN 2891
                    WHEN 2973 THEN 2974
                    WHEN 1996 THEN 3776
                    WHEN 2908 THEN 3784
                    WHEN 2241 THEN 3788
                    WHEN 2082 THEN 4631
                    WHEN 13684 THEN 5066
                    WHEN 4655 THEN 5216
                    WHEN 448 THEN 5317
                    WHEN 1657 THEN 12847
                    WHEN 1805 THEN 14002
                    WHEN 17632 THEN 18290
                    WHEN 383 THEN 18642
                    WHEN 18147 THEN 18714
                    WHEN 17632 THEN 18716
                    WHEN 18979 THEN 19049
                    WHEN 2537 THEN 19050
                    WHEN 2537 THEN 19208
                    WHEN 13982 THEN 19332
                    WHEN 4135 THEN 19342
                    WHEN 17632 THEN 19375
                    WHEN 17632 THEN 19405
                    WHEN 18147 THEN 19733
                    WHEN 17632 THEN 19735
                    WHEN 2885 THEN 20612
                    WHEN 1055 THEN 21421
                    WHEN 17632 THEN 21614
                    WHEN 1988 THEN 21676
                    WHEN 21931 THEN 21932
                    WHEN 19323 THEN 22126
                    WHEN 19260 THEN 22127
                    WHEN 19781 THEN 22129
                    WHEN 19786 THEN 22134
                    WHEN 20587 THEN 22135
                    WHEN 21687 THEN 22136
                    WHEN 20588 THEN 22140
                    WHEN 21238 THEN 22143
                    WHEN 21693 THEN 22149
                    WHEN 19785 THEN 22150
                    WHEN 21552 THEN 22151
                    WHEN 21553 THEN 22152
                    WHEN 19780 THEN 22153
                    WHEN 21886 THEN 22157
                    WHEN 21834 THEN 22169
                    WHEN 21833 THEN 22170
                    WHEN 19298 THEN 22225
                    WHEN 19783 THEN 22226
                    WHEN 21280 THEN 22227
                    WHEN 21900 THEN 22228
                    WHEN 21554 THEN 22229
                    WHEN 21903 THEN 22235
                END
            )
            WHERE college_id in (
                    19257,721,721,1498,1498,1818,2148,3588,721,2132,707,1587,1818,707,2566,2566,2973,1587,3588,2132,2149,
                    1462,1498,2973,781,3186,2973,1996,2908,2241,2082,13684,4655,448,1657,1805,17632,383,18147,17632,
                    18979,2537,2537,13982,4135,17632,17632,18147,17632,2885,1055,17632,1988,21931,19323,19260,19781,
                    19786,20587,21687,20588,21238,21693,19785,21552,21553,19780,21886,21834,21833,19298,19783,21280,
                    21900,21554,21903
                )
        ");
    }

}
