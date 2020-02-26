<?php

use App\Models\City;
use App\Models\PersonHasPlace;
use App\Models\Place;
use App\Models\WrongAddress;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MigratePlaces extends Migration
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
        Place::unguard(true);

        DB::table('cadastro.endereco_pessoa')
            ->select([
                'endereco_pessoa.*',
                'logradouro.*',
                'tipo_logradouro.*',
                'cep_logradouro.*',
                'bairro.nome as nome_bairro',
                'bairro.idmun as idmun_bairro',
                'bairro.iddis',
            ])
            ->join('public.logradouro', 'logradouro.idlog', '=', 'endereco_pessoa.idlog')
            ->join('urbano.tipo_logradouro', 'tipo_logradouro.idtlog', '=', 'logradouro.idtlog')
            ->join('urbano.cep_logradouro', function (JoinClause $join) {
                $join->on('cep_logradouro.idlog', '=', 'logradouro.idlog')
                    ->on('cep_logradouro.cep', '=', 'endereco_pessoa.cep');
            })
            ->join('public.bairro', 'bairro.idbai', '=', 'endereco_pessoa.idbai')
            ->join('cadastro.pessoa', 'pessoa.idpes', '=', 'endereco_pessoa.idpes')
            ->orderBy('logradouro.idlog')
            ->chunk(100, function ($collection) {
                /** @var Collection $collection */
                $collection->each(function ($logradouro) {
                    $has = PersonHasPlace::query()->where([
                        'person_id' => $logradouro->idpes,
                        'type' => $logradouro->tipo,
                    ])->first();

                    $place = $has ? Place::query()->find($has->place_id) : new Place();

                    $complement = array_filter([
                        trim($logradouro->complemento),
                        trim($logradouro->letra),
                        trim($logradouro->bloco),
                        trim($logradouro->andar),
                        trim($logradouro->apartamento),
                    ]);

                    $place->fill([
                        'city_id' => $logradouro->idmun,
                        'address' => trim(trim($logradouro->descricao) . ' ' . trim($logradouro->nome)),
                        'number' => $logradouro->numero,
                        'complement' => join(', ', $complement),
                        'neighborhood' => trim($logradouro->nome_bairro),
                        'postal_code' => $logradouro->cep,
                        'latitude' => $logradouro->latitude,
                        'longitude' => $logradouro->longitude,
                    ])->saveOrFail();

                    PersonHasPlace::query()->updateOrCreate([
                        'person_id' => $logradouro->idpes,
                        'type' => $logradouro->tipo,
                    ], [
                        'place_id' => $place->getKey(),
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
        Place::query()->truncate();
    }
}
