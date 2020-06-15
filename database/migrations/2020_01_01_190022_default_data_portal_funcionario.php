<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DefaultDataPortalFuncionario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => DefaultPortalFuncionarioTableSeeder::class, '--force' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::raw('TRUNCATE portal.funcionario CASCADE;');
    }
}
