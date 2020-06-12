<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class GenerateSlugNameToPerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('update cadastro.pessoa set slug = lower(public.unaccent(nome)) where true;');
    }
}
