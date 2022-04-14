<?php

namespace iEducar\Legacy;

use App\Models\City;
use App\Models\Country;
use App\Models\State;

trait SelectOptions
{
    public function getCountries()
    {
        return Country::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getStates($country)
    {
        return State::query()
            ->where('country_id', $country)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getCities($state)
    {
        return City::query()
            ->where('state_id', $state)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
