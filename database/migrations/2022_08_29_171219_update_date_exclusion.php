<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        $serie = <<<'SQL'
        update pmieducar.serie
        set data_exclusao = updated_at
        WHERE ativo = 0
        AND data_exclusao IS NULL
    SQL;

        DB::unprepared($serie);

        $curso = <<<'SQL'
        update pmieducar.curso
        set data_exclusao = updated_at
        WHERE ativo = 0
        AND data_exclusao IS NULL
    SQL;

        DB::unprepared($curso);
    }
};
