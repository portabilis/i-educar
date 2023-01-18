<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsereSeriesMultiInexistentesNasEscolas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            INSERT INTO escola_serie (
                ref_cod_escola,
                ref_cod_serie,
                ref_usuario_cad,
                ativo,
                anos_letivos,
                data_cadastro
            )
            SELECT
                t.ref_ref_cod_escola,
                s.cod_serie,
                1 AS ref_usuario_cad,
                1 AS ativo,
                array_agg(DISTINCT t.ano) AS anos_letivos,
                now()
            FROM pmieducar.turma t
            JOIN pmieducar.serie s ON s.cod_serie = t.ref_ref_cod_serie_mult
            WHERE t.ref_ref_cod_serie_mult IS NOT NULL
            AND NOT EXISTS (
                SELECT 1
                FROM pmieducar.escola_serie es
                WHERE es.ref_cod_serie = s.cod_serie
                AND es.ref_cod_escola = t.ref_ref_cod_escola
            )
            GROUP BY
                t.ref_ref_cod_escola,
                s.cod_serie;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
