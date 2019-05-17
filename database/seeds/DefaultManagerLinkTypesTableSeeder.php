<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultManagerLinkTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manager_link_types')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Concursado/efetivo/estável']
        );

        DB::table('manager_link_types')->updateOrInsert(
            ['id' => 2],
            ['name' => 'Contrato temporário']
        );

        DB::table('manager_link_types')->updateOrInsert(
            ['id' => 3],
            ['name' => 'Contrato terceirizado']
        );

        DB::table('manager_link_types')->updateOrInsert(
            ['id' => 4],
            ['name' => 'Contrato CLT']
        );
    }
}
