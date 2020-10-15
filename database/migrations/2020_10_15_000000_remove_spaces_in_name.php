<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveSpacesInName extends Migration
{
    private function sql()
    {
        return <<<SQL
            update cadastro.pessoa 
            set nome = trim(nome),
                slug = trim(slug)
            where substring(nome, length(nome), 1) = ' ' 
            or substring(nome, 1, 1) = ' ';
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
