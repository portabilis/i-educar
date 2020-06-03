<?php

use App\Models\City;
use App\Models\State;
use App\Support\Database\IncrementSequence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateCities extends Migration
{
    use IncrementSequence;

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
        City::unguard(true);

        DB::table('public.municipio')->orderBy('idmun')->chunk(100, function ($collection) {
            /** @var Collection $collection */
            $ids = $collection->map(function ($municipio) {
                return $municipio->sigla_uf;
            })->unique()->values();

            $states = State::query()->whereIn('abbreviation', $ids)->pluck('id', 'abbreviation');

            $collection->each(function ($municipio) use ($states) {
                City::query()->updateOrCreate([
                    'id' => $municipio->idmun,
                ], [
                    'state_id' => $states->get($municipio->sigla_uf),
                    'name' => trim($municipio->nome),
                    'ibge_code' => $municipio->cod_ibge,
                ]);
            });
        });

        $this->incrementSequence(City::class);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        City::query()->truncate();
    }
}
