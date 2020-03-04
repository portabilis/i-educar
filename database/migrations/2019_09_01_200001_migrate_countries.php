<?php

use App\Models\Country;
use App\Support\Database\IncrementSequence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateCountries extends Migration
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
        Country::unguard(true);

        DB::table('public.pais')->orderBy('idpais')->chunk(100, function ($collection) {
            /** @var Collection $collection */
            $collection->each(function ($pais) {
                Country::query()->updateOrCreate([
                    'id' => $pais->idpais,
                ], [
                    'name' => trim($pais->nome),
                    'ibge_code' => $pais->cod_ibge,
                ]);
            });
        });

        $this->incrementSequence(Country::class);
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
