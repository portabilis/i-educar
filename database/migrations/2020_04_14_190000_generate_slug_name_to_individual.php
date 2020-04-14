<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GenerateSlugNameToIndividual extends Migration
{
    /**
     * @return string
     */
    private function getSql()
    {
        return <<<SQL
select lower(public.unaccent(f.nome_social)) || ' ' || p.slug 
from cadastro.fisica f
inner join cadastro.pessoa p 
on p.idpes = f.idpes 
where nome_social is not null and nome_social <> ''
SQL;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared($this->getSql());
    }
}
