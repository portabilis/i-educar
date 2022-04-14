<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultManagerRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
}
