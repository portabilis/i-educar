<?php

class Portabilis_View_Helper_DynamicInput_ComponenteCurricular extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function getResourceId($id = null)
    {
        if (!$id && $this->viewInstance->ref_cod_componente_curricular) {
            $id = $this->viewInstance->ref_cod_componente_curricular;
        }

        return $id;
    }

    protected function getOptions($turmaId, $resources)
    {
        return $this->insertOption(null, 'Selecione um componente curricular', []);
    }

    public function componenteCurricular($options = [])
    {
        $defaultOptions = [
            'id' => null,
            'turmaId' => null,
            'options' => [],
            'resources' => []
        ];

        $options = $this->mergeOptions($options, $defaultOptions);
        $resources = $this->getOptions($options['turmaId'], $options['resources']);

        $defaultSelectOptions = [
            'id' => 'ref_cod_componente_curricular',
            'label' => 'Componente Curricular',
            'componentes_curriculares' => $resources,
            'value' => $this->getResourceId($options['id']),
            'callback' => '',
            'inline' => false,
            'label_hint' => '',
            'input_hint' => '',
            'disabled' => false,
            'required' => true,
            'multiple' => false
        ];

        $selectOptions = $this->mergeOptions($options['options'], $defaultSelectOptions);

        $this->viewInstance->campoLista(...array_values($selectOptions));
    }
}
