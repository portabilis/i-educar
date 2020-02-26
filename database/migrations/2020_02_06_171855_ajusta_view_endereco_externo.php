<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AjustaViewEnderecoExterno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.endereco_externo;'
        );

        DB::unprepared(
            'create or replace view cadastro.endereco_externo as
            select
                null::integer as idpes,
                null::integer as tipo,
                null::integer as idtlog,
                null::varchar as logradouro,
                null::varchar as numero,
                null::varchar as letra,
                null::varchar as complemento,
                null::varchar as bairro,
                null::varchar as cep,
                null::varchar as cidade,
                null::varchar as sigla_uf,
                null::date as reside_desde,
                null::integer as idpes_rev,
                null::timestamp as data_rev,
                null::bpchar as origem_gravacao,
                null::integer as idpes_cad,
                null::timestamp as data_cad,
                null::bpchar as operacao,
                null::varchar as bloco,
                null::integer as andar,
                null::integer as apartamento,
                null::integer as zona_localizacao;
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.endereco_externo;'
        );

        DB::unprepared(
            'create or replace view cadastro.endereco_externo as
            select
                null::integer as idpes,
                null::integer as tipo,
                null::integer as idtlog,
                null::varchar as logradouro,
                null::integer as numero,
                null::varchar as letra,
                null::varchar as complemento,
                null::varchar as bairro,
                null::integer as cep,
                null::varchar as cidade,
                null::varchar as sigla_uf,
                null::date as reside_desde,
                null::integer as idpes_rev,
                null::timestamp as data_rev,
                null::bpchar as origem_gravacao,
                null::integer as idpes_cad,
                null::timestamp as data_cad,
                null::bpchar as operacao,
                null::varchar as bloco,
                null::integer as andar,
                null::integer as apartamento,
                null::integer as zona_localizacao;
            '
        );
    }
}
