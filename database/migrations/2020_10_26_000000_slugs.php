<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Slugs extends Migration
{
    private function sql()
    {
        return <<<SQL
            update cadastro.pessoa
            set slug = trim(REGEXP_REPLACE(slug, '[^[:alnum:][:space:]]', '', 'g'))
            where slug ~ '[^[:alnum:][:space:]]'
SQL;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared($this->sql());
    }
}
