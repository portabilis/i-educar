<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateDuplicateCollegeEducacenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         *  ATUALIZA DUPLICADOS DA BASE DO IEDUCAR
         */
        DB::unprepared("
            UPDATE public.employee_graduations SET college_id = 5747 WHERE college_id = 5812;
            UPDATE public.employee_graduations SET college_id = 5748 WHERE college_id = 5813;
            UPDATE public.employee_graduations SET college_id = 5749 WHERE college_id = 5815;
            UPDATE public.employee_graduations SET college_id = 5750 WHERE college_id = 5816;
        ");

        /**
         * ATUALIZA DUPLICADOS DA BASE DO CENSO
         */
        DB::unprepared("
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 10071
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4539
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1013
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3316
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1021
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1143
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 10251
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5007
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 10323
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5353
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1036
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14236
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1036
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3461
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1036
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3474
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1036
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3484
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1059
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4994
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 10613
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4930
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 10923
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5435
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11289
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5031
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11308
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5293
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11428
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5487
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11429
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5364
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11544
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5528
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11584
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5354
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11645
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5397
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11807
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5497
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11818
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5486
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1183
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4643
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11841
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5094
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11861
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5357
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11862
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4972
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11902
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5526
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11951
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3500
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12052
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5493
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12136
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5060
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12189
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5033
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12249
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5360
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12346
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5265
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1239
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3249
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12430
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5390
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1243
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2622
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12522
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5181
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12533
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5564
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1258
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1589
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12597
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 16107
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12597
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5437
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12611
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5540
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12625
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5375
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12661
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5445
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12723
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5532
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12735
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4287
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12766
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5504
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12791
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5522
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12803
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5418
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12847
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5499
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12928
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2453
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 12946
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5547
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1307
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1802
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13106
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5525
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13417
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5546
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13467
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5425
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1353
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3621
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1353
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3622
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13684
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4866
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13724
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5584
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13743
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4864
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1380
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3248
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 13938
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4176
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1411
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4939
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14148
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14189
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14156
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14191
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14165
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3361
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1425
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5250
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1431
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4159
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14342
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5006
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1435
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3251
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1492
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5067
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1517
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5299
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 15562
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5011
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1561
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2010
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 15705
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3871
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 15833
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4608
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1606
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 811
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1669
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4233
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 16781
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4667
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1689
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2686
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1696
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3711
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17014
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3326
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17165
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3683
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1721
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2106
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17322
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4961
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17614
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5152
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17631
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4973
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1767
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5150
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17715
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21583
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17777
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5132
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1777
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3982
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1790
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1715
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 18035
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5126
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 18059
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3508
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 18059
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4561
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1816
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3277
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1827
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5143
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1831
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2083
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 18679
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4089
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1877
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5278
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 19219
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3124
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1931
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5567
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1937
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5596
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1944
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5235
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1950
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2701
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1965
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5510
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 19781
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4862
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2015
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2250
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2019
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3660
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2050
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5149
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2116
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5583
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21550
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4216
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21822
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1389
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2182
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3265
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2182
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4125
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21903
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4861
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21957
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5416
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2237
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4638
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2242
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3273
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 225
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1408
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2302
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4885
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2346
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3212
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2410
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2315
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2433
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4886
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2451
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3310
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2534
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3747
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2535
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3571
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2549
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3037
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2583
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5471
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2593
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3269
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2595
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4105
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2599
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2705
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2622
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5457
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 282
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3289
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2841
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5624
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 302
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2504
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3077
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4078
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3271
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4364
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3277
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4076
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3310
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4245
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3332
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3296
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3347
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5255
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3359
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3457
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3377
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5203
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3412
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3763
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3461
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5595
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3483
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14237
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3485
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14199
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3564
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3565
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3628
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3629
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3652
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3653
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 373
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5590
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3743
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2610
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3749
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1181
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3751
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4160
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3790
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4244
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3829
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3949
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 382
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3964
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3924
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4882
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3951
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4225
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3952
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4881
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3953
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4624
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3955
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3315
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3976
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14005
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3982
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4991
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3983
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1566
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3983
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1635
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3987
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3988
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4010
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 436
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4023
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 6490
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4028
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3401
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4092
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5058
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4150
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 15682
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4279
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5351
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4330
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14367
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4368
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4328
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4410
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5279
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4424
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4783
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4447
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4448
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4516
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5597
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4534
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4180
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4536
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5140
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4619
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5563
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4669
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4074
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4721
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3370
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4803
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4905
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4834
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 22022
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 486
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 2061
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4873
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 18049
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4977
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5089
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4983
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4215
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5055
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 4937
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5090
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5436
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5180
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 11817
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5220
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 21492
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5225
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5226
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5242
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 486
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5306
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 14201
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5396
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5463
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5409
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5410
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5411
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5414
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5447
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 16881
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5587
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5588
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 747
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 3198
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 778
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 17952
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 881
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 5551
            );
            
            UPDATE public.employee_graduations
            SET college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 941
            )
            WHERE college_id = (
                SELECT id
                FROM modules.educacenso_ies ei
                WHERE ei.ies_id = 1338
            );
        ");
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
