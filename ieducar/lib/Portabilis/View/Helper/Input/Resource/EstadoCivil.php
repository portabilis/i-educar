<?php

use App\Models\LegacyMaritalStatus;

class Portabilis_View_Helper_Input_Resource_EstadoCivil extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            $resources = LegacyMaritalStatus::orderBy('descricao')->pluck('descricao', 'ideciv')->toArray();
        }

        return $this->insertOption(null, 'Estado civil', $resources);
    }

    public function estadoCivil($options = [])
    {
        parent::select($options);
    }
}
