<?php

use App\Models\Country;
use App\Models\State;

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_Uf extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $states = State::query()->where('country_id', Country::BRASIL)->get()->values();

            $resources = Portabilis_Array_Utils::setAsIdValue($states->toArray(), 'abbreviation', 'abbreviation');
        }

        return $this->insertOption(null, 'Estado', $resources);
    }

    public function uf($options = [])
    {
        parent::select($options);
    }
}
