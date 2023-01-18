<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::unprepared('
            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 71007), 19101)
            WHERE ARRAY[71007] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 18101), 19201)
            WHERE ARRAY[18101] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 18102), 19202)
            WHERE ARRAY[18102] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 18103), 19203)
            WHERE ARRAY[18103] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 13302), 20101)
            WHERE ARRAY[13302] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 31002), 31016)
            WHERE ARRAY[31002] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 16103), 13306)
            WHERE ARRAY[16103] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 71004), 19106)
            WHERE ARRAY[71004] <@ atividades_complementares;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::unprepared('
            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 19101), 71007)
            WHERE ARRAY[19101] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 19201), 18101)
            WHERE ARRAY[19201] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 19202), 18102)
            WHERE ARRAY[19202] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 19203), 18103)
            WHERE ARRAY[19203] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 20101), 13302)
            WHERE ARRAY[20101] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 31016), 31002)
            WHERE ARRAY[31016] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 13306), 16103)
            WHERE ARRAY[13306] <@ atividades_complementares;

            UPDATE pmieducar.turma
            SET atividades_complementares = array_append(array_remove(atividades_complementares, 19106), 71004)
            WHERE ARRAY[19106] <@ atividades_complementares;
        ');
    }
};
