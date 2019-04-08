<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertManagerLinkTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_link_types')->updateOrInsert(
            [
                [
                    'id' => 1,
                    'name' => 'Concursado/efetivo/estável'
                ],
                [
                    'id' => 2,
                    'name' => 'Contrato temporário'
                ],
                [
                    'id' => 3,
                    'name' => 'Contrato terceirizado'
                ],
                [
                    'id' => 4,
                    'name' => 'Contrato CLT'
                ],
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
        //
    }
}
