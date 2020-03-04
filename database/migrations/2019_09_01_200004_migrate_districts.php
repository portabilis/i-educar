<?php

use App\Models\District;
use App\Support\Database\IncrementSequence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class MigrateDistricts extends Migration
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
        District::unguard(true);

        DB::table('public.distrito')->orderBy('iddis')->chunk(100, function ($collection) {
            /** @var Collection $collection */
            $collection->each(function ($distrito) {
                District::query()->updateOrCreate([
                    'id' => $distrito->iddis,
                ], [
                    'city_id' => $distrito->idmun,
                    'name' => trim($distrito->nome),
                    'ibge_code' => $distrito->cod_ibge ?: null,
                ]);
            });
        });

        $this->incrementSequence(District::class);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        District::query()->truncate();
    }
}
