<?php

class Portabilis_View_Helper_DynamicInput_Vinculo extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        $sql = 'select cod_funcionario_vinculo, nm_vinculo from portal.funcionario_vinculo';

        $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql);
        $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_funcionario_vinculo', 'nm_vinculo');

        return $this->insertOption(null, 'Selecione', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Vínculo']];
    }

    public function vinculo($options = [])
    {
        parent::select($options);
    }
}
