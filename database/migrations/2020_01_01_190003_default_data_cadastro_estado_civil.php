<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DefaultDataCadastroEstadoCivil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', ['--class' => DefaultCadastroEstadoCivilTableSeeder::class, '--force' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::raw('TRUNCATE cadastro.estado_civil CASCADE;');
    }
}
