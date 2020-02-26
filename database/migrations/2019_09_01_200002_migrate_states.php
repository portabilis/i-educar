<?php

use App\Models\State;
use Illuminate\Support\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateStates extends Migration
{
    /**
     * Enables, if supported, wrapping the migration within a transaction.
     *
     * @var bool
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('public.uf')->orderBy('sigla_uf')->chunk(100, function ($collection) {
            /** @var Collection $collection */
            $collection->each(function ($uf) {
                State::query()->updateOrCreate([
                    'country_id' => $uf->idpais,
                    'abbreviation' => $uf->sigla_uf,
                ], [
                    'name' => trim($uf->nome),
                    'ibge_code' => $uf->cod_ibge,
                ]);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        State::query()->truncate();
    }
}
