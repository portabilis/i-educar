<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaDescricoesDeCamposDeConfiguracoesDoPreMatricula extends Migration
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
            SET description = (
                CASE key
                    WHEN 'prematricula.active' THEN 'Ativa o Pré-matrícula Digital'
                    WHEN 'prematricula.city' THEN 'Município'
                    WHEN 'prematricula.state' THEN 'Estado (UF)'
                    WHEN 'prematricula.map.lat' THEN 'Latitude (Mapa)'
                    WHEN 'prematricula.map.lng' THEN 'Longitude (Mapa)'
                    WHEN 'prematricula.map.zoom' THEN 'Zoom (Mapa)'
                END
            )
            WHERE key IN (
                'prematricula.active',
                'prematricula.city',
                'prematricula.state',
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
                SET description = ''
            WHERE key IN (
                'prematricula.active',
                'prematricula.city',
                'prematricula.state',
                'prematricula.map.lat',
                'prematricula.map.lng',
                'prematricula.map.zoom'
            )
        ");
    }
}
