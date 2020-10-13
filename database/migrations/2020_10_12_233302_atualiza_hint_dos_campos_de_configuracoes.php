<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaHintDosCamposDeConfiguracoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE public.settings
            SET hint = (
                CASE key
                    WHEN 'prematricula.map.lat' THEN 'Informe a latitude em que o mapa deve ser centralizado'
                    WHEN 'prematricula.map.lng' THEN 'Informe a longitude em que o mapa deve ser centralizado'
                    WHEN 'prematricula.map.zoom' THEN 'Informe o valor máximo de aproximação do mapa (territórios maiores devem ter o zoom reduzido)'
                END
            )
            WHERE key IN (
                'prematricula.map.lat',
                'prematricula.map.lng',
                'prematricula.map.zoom'
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            UPDATE public.settings
            SET hint = null
            WHERE key IN (
                'prematricula.map.lat',
                'prematricula.map.lng',
                'prematricula.map.zoom'
            )
        ");
    }
}
