<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GenerateSlugNameToPersonV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('update cadastro.pessoa set slug = lower(public.unaccent(nome)) where slug IS NULL;');
    }
}
