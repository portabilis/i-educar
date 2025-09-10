<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [1, 2, 3])
            ->update([
                'etapa_agregada' => 301,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [14, 15, 16, 17, 18, 19, 20, 21, 41])
            ->update([
                'etapa_agregada' => 302,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [22, 23, 56])
            ->update([
                'etapa_agregada' => 303,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [25, 26, 27, 28, 29])
            ->update([
                'etapa_agregada' => 304,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [35, 36, 37, 38])
            ->update([
                'etapa_agregada' => 305,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [69, 70, 72, 71, 74, 73, 67])
            ->update([
                'etapa_agregada' => 306,
            ]);
        DB::table('pmieducar.turma')->whereIn('etapa_educacenso', [39, 40, 64, 68])
            ->update([
                'etapa_agregada' => 308,
            ]);
    }
};
