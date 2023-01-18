<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigraTurmasMultisseriadasParaNovaEstrutura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            INSERT INTO pmieducar.turma_serie (
                turma_id,
                serie_id,
                escola_id,
                boletim_id,
                boletim_diferenciado_id
            )
            SELECT
                t.cod_turma,
                s.cod_serie,
                t.ref_ref_cod_escola,
                t.tipo_boletim,
                t.tipo_boletim_diferenciado
            FROM pmieducar.turma t
            JOIN pmieducar.serie s ON s.cod_serie IN (t.ref_ref_cod_serie, t.ref_ref_cod_serie_mult)
            WHERE t.ref_ref_cod_serie_mult IS NOT NULL
            ORDER BY 1,2;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            DELETE
            FROM pmieducar.turma_serie ts
            WHERE EXISTS (
                SELECT 1
                FROM pmieducar.turma t
                JOIN pmieducar.serie s ON s.cod_serie IN (t.ref_ref_cod_serie, t.ref_ref_cod_serie_mult)
                WHERE t.ref_ref_cod_serie_mult IS NOT NULL
                AND s.cod_serie = ts.serie_id
                AND t.cod_turma = ts.turma_id
            );
        ');
    }
}
