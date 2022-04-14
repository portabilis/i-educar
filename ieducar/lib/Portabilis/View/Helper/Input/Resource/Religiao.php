<?php

class Portabilis_View_Helper_Input_Resource_Religiao extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $resources = new clsPmieducarReligiao();
            $resources = $resources->lista(null, null, null, null, null, null, null, null, 1);
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_religiao', 'nm_religiao');
        }

        return $this->insertOption(null, 'Religião', $resources);
    }

    public function religiao($options = [])
    {
        parent::select($options);
    }
}
