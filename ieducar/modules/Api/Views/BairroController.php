<?php

use App\Models\Place;

class BairroController extends ApiCoreController
{
    protected function getNeighborhoods()
    {
        return Place::query()
            ->select('neighborhood', 'city_id')
            ->with('city')
            ->whereUnaccent('neighborhood', $this->getQueryString('query'))
            ->groupBy('city_id', 'neighborhood')
            ->limit(15)
            ->get()
            ->mapWithKeys(function ($place) {
                return [
                    "{$place->neighborhood} / {$place->city_id}" => "{$place->neighborhood} / {$place->city->name}",
                ];
            })
            ->all();
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bairro-search')) {
            $this->appendResponse([
                'result' => $this->getNeighborhoods(),
            ]);
        } else {
            $this->notImplementedOperationError();
        }
    }
}
