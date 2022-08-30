<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        $serie = <<<SQL
        update pmieducar.serie
        set data_exclusao = updated_at
        WHERE ativo = 0
        AND data_exclusao IS NULL
    SQL;

        DB::unprepared($serie);

        $curso = <<<SQL
        update pmieducar.curso
        set data_exclusao = updated_at
        WHERE ativo = 0
        AND data_exclusao IS NULL
    SQL;

        DB::unprepared($curso);
    }
};
