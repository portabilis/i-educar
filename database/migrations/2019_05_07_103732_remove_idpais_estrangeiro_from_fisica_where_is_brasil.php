<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveIdpaisEstrangeiroFromFisicaWhereIsBrasil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE cadastro.fisica
            SET idpais_estrangeiro = NULL
            FROM public.pais
            WHERE fisica.idpais_estrangeiro = pais.idpais
            AND (nome ILIKE 'Brasil' OR cod_ibge = 76)
        ");
    }
}
