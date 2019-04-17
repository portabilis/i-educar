<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertManagerRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('manager_roles')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Diretor(a)']
        );

        DB::table('manager_roles')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Outro cargo']
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
